<?php

namespace Gdbots\Tests\Pbj\Integration;

use Aws\Credentials\Credentials;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Gdbots\Pbj\Marshaler\DynamoDb\ItemMarshaler;
use Gdbots\Tests\Pbj\FixtureLoader;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;

class DynamoDbTest extends \PHPUnit_Framework_TestCase
{
    use FixtureLoader;

    /** @var DynamoDbClient */
    protected static $client;

    /** @var string */
    protected static $tableName;

    /** @var ItemMarshaler */
    protected $marshaler;

    /** @var EmailMessage */
    protected $message;

    public static function setUpBeforeClass()
    {
        $key = getenv('AWS_KEY');
        $secret = getenv('AWS_SECRET');
        self::$tableName = getenv('DYNAMODB_TABLE') ?: 'pbj_tests';

        if (empty($key) || empty($secret)) {
            return;
        }

        self::$client = new DynamoDbClient([
            'credentials' => new Credentials($key, $secret),
            'region' => 'us-west-2',
            'version' => '2012-08-10'
        ]);

        self::createTable();
    }

    public static function tearDownAfterClass()
    {
        if (null === self::$client) {
            return;
        }

        self::deleteTable();
    }

    /**
     * Create the dynamodb table before tests run.
     */
    protected static function createTable()
    {
        try {
            self::$client->describeTable(['TableName' => self::$tableName]);
            return;
        } catch (DynamoDbException $e)  {
            // table doesn't exist, create it below
        }

        self::$client->createTable([
            'TableName' => self::$tableName,
            'AttributeDefinitions' => [
                ['AttributeName' => 'id', 'AttributeType' => 'S'],
            ],
            'KeySchema' => array(
                ['AttributeName' => 'id', 'KeyType' => 'HASH'],
            ),
            'ProvisionedThroughput' => [
                'ReadCapacityUnits'  => 5,
                'WriteCapacityUnits' => 5
            ]
        ]);

        self::$client->waitUntil('TableExists', ['TableName' => self::$tableName]);
    }

    /**
     * Delete the test table after tests complete.
     */
    protected static function deleteTable()
    {
        self::$client->deleteTable(['TableName' => self::$tableName]);
        self::$client->waitUntil('TableNotExists', ['TableName' => self::$tableName]);
    }

    public function setup()
    {
        if (null === self::$client) {
            $this->markTestSkipped('AWS_KEY or AWS_SECRET was not supplied, skipping integration test.');
            return;
        }

        $this->marshaler = new ItemMarshaler();
        $this->message = $this->createEmailMessage();
    }

    public function testPutItem()
    {
        try {
            $item = $this->marshaler->marshal($this->message);
            self::$client->putItem([
                'TableName' => self::$tableName,
                'Item' => $item
            ]);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }
    }

    public function testGetItem()
    {
        try {
            $result = self::$client->getItem([
                'TableName' => self::$tableName,
                'ConsistentRead' => true,
                'Key' => ['id' => ['S' => $this->message->get('id')->toString()]]
            ]);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }

        $this->assertSame($result['Item']['id']['S'], $this->message->get('id')->toString());
        $message = $this->marshaler->unmarshal($result['Item']);

        foreach ($this->message->schema()->getFields() as $field) {
            $expected = $this->message->get($field->getName());
            $actual = $message->get($field->getName());

            if ($field->isASet()) {
                sort($expected);
                sort($actual);
            }

            $this->assertSame(json_encode($expected), json_encode($actual));
        }

        //echo json_encode($message, JSON_PRETTY_PRINT);
    }
}
