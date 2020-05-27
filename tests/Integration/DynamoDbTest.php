<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj\Integration;

use Aws\Credentials\Credentials;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Gdbots\Pbj\Marshaler\DynamoDb\ItemMarshaler;
use Gdbots\Tests\Pbj\FixtureLoader;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;
use PHPUnit\Framework\TestCase;

class DynamoDbTest extends TestCase
{
    use FixtureLoader;

    protected static ?DynamoDbClient $client = null;
    protected static ?string $tableName = null;
    protected ?ItemMarshaler $marshaler = null;
    protected ?EmailMessage $message = null;

    public static function setUpBeforeClass(): void
    {
        $key = getenv('AWS_KEY');
        $secret = getenv('AWS_SECRET');
        self::$tableName = getenv('DYNAMODB_TABLE') ?: 'pbj_tests';

        if (empty($key) || empty($secret)) {
            return;
        }

        self::$client = new DynamoDbClient([
            'credentials' => new Credentials($key, $secret),
            'region'      => 'us-west-2',
            'version'     => '2012-08-10',
        ]);

        self::createTable();
    }

    public static function tearDownAfterClass(): void
    {
        if (null === self::$client) {
            return;
        }

        self::deleteTable();
    }

    /**
     * Create the dynamodb table before tests run.
     */
    protected static function createTable(): void
    {
        try {
            self::$client->describeTable(['TableName' => self::$tableName]);
            return;
        } catch (DynamoDbException $e) {
            // table doesn't exist, create it below
        }

        self::$client->createTable([
            'TableName'             => self::$tableName,
            'AttributeDefinitions'  => [
                ['AttributeName' => 'id', 'AttributeType' => 'S'],
            ],
            'KeySchema'             => [
                ['AttributeName' => 'id', 'KeyType' => 'HASH'],
            ],
            'ProvisionedThroughput' => [
                'ReadCapacityUnits'  => 5,
                'WriteCapacityUnits' => 5,
            ],
        ]);

        self::$client->waitUntil('TableExists', ['TableName' => self::$tableName]);
    }

    /**
     * Delete the test table after tests complete.
     */
    protected static function deleteTable(): void
    {
        self::$client->deleteTable(['TableName' => self::$tableName]);
        self::$client->waitUntil('TableNotExists', ['TableName' => self::$tableName]);
    }

    public function setUp(): void
    {
        if (null === self::$client) {
            $this->markTestSkipped('AWS_KEY or AWS_SECRET was not supplied, skipping integration test.');
            return;
        }

        $this->marshaler = new ItemMarshaler();
        $this->message = $this->createEmailMessage();
    }

    public function testPutItem(): void
    {
        try {
            $item = $this->marshaler->marshal($this->message);
            self::$client->putItem([
                'TableName' => self::$tableName,
                'Item'      => $item,
            ]);
        } catch (\Throwable $e) {
            $this->fail($e->getMessage());
            return;
        }
    }

    public function testGetItem(): void
    {
        try {
            $result = self::$client->getItem([
                'TableName'      => self::$tableName,
                'ConsistentRead' => true,
                'Key'            => ['id' => ['S' => $this->message->get('id')->toString()]],
            ]);
        } catch (\Throwable $e) {
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
