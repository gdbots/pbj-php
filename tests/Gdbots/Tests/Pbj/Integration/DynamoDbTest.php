<?php

namespace Gdbots\Tests\Pbj\Integration;

use Aws\Common\Enum\Region;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Enum\KeyType;
use Aws\DynamoDb\Enum\Type;
use Aws\DynamoDb\Exception\ResourceNotFoundException;
use Gdbots\Pbj\Marshaler\DynamoDb\ItemMarshaler;
use Gdbots\Tests\Pbj\FixtureLoader;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;
use Gdbots\Tests\Pbj\Fixtures\MapsMessage;
use Gdbots\Tests\Pbj\Fixtures\NestedMessage;

class DynamoDbTest extends \PHPUnit_Framework_TestCase
{
    use FixtureLoader;

    /** @var DynamoDbClient */
    private $client;

    /** @var ItemMarshaler */
    private $marshaler;

    /** @var EmailMessage */
    private $message;

    /** @var string */
    private $tableName;

    public function setup()
    {
        EmailMessage::schema();
        NestedMessage::schema();
        MapsMessage::schema();

        $key = getenv('AWS_KEY');
        $secret = getenv('AWS_SECRET');
        $this->tableName = getenv('DYNAMODB_TABLE') ?: 'pbj_tests';

        if (empty($key) || empty($secret)) {
            $this->markTestSkipped('AWS_KEY or AWS_SECRET was not supplied, skipping integration test.');
            return;
        }

        $this->client = DynamoDbClient::factory([
            'key'    => $key,
            'secret' => $secret,
            'region' => Region::US_WEST_2
        ]);
        $this->createTable();

        $this->marshaler = new ItemMarshaler();
        $this->message = $this->createEmailMessage();
    }

    /**
     * Creates the DynamoDb table
     */
    private function createTable()
    {
        try {
            $this->client->describeTable(['TableName' => $this->tableName]);
            return;
        } catch (ResourceNotFoundException $e)  {
            // table doesn't exist, create it below
        }

        $this->client->createTable([
            'TableName' => $this->tableName,
            'AttributeDefinitions' => [
                ['AttributeName' => 'id', 'AttributeType' => Type::STRING],
            ],
            'KeySchema' => array(
                ['AttributeName' => 'id', 'KeyType' => KeyType::HASH],
            ),
            'ProvisionedThroughput' => [
                'ReadCapacityUnits'  => 5,
                'WriteCapacityUnits' => 5
            ]
        ]);

        $this->client->waitUntil('TableExists', ['TableName' => $this->tableName]);
    }

    /**
     * Delete the test table after tests complete.
     */
    private function deleteTable()
    {
        $this->client->deleteTable($this->tableName);
        $this->client->waitUntil('TableNotExists', ['TableName' => $this->tableName]);
    }

    public function testPutItem()
    {
        $item = $this->marshaler->marshal($this->message);
        //echo json_encode($item, JSON_PRETTY_PRINT);

        try {
            $this->client->putItem([
                'TableName' => $this->tableName,
                'Item' => $item
            ]);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }
    }

    /**
     * @depends testPutItem
     */
    public function testGetItem()
    {
        try {
            $result = $this->client->getItem([
                'TableName' => $this->tableName,
                'ConsistentRead' => true,
                'Key' => ['id' => ['S' => $this->message->getMessageId()->toString()]]
            ]);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }

        $this->assertSame($result['Item']['id']['S'], $this->message->getMessageId()->toString());
    }
}
