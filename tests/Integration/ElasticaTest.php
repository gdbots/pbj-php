<?php

namespace Gdbots\Tests\Pbj\Integration;

use Elastica\Client;
use Elastica\Index;
use Gdbots\Pbj\Marshaler\Elastica\DocumentMarshaler;
use Gdbots\Pbj\Marshaler\Elastica\MappingFactory;
use Gdbots\Tests\Pbj\FixtureLoader;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;
use PHPUnit\Framework\TestCase;

class ElasticaTest extends TestCase
{
    use FixtureLoader;

    /** @var Index */
    protected static $index;

    /** @var DocumentMarshaler */
    protected $marshaler;

    /** @var EmailMessage */
    protected $message;

    public static function setUpBeforeClass()
    {
        $host = getenv('ELASTIC_HOST');
        $port = getenv('ELASTIC_PORT') ?: 9200;
        $indexName = getenv('ELASTIC_INDEX') ?: 'pbj_tests';

        if (empty($host) || empty($port)) {
            return;
        }

        $client = new Client(['connections' => [['host' => $host, 'port' => $port]]]);
        self::$index = $client->getIndex($indexName);
        self::createIndex();
    }

    public static function tearDownAfterClass()
    {
        if (null === self::$index) {
            return;
        }
        //self::deleteIndex();
    }

    /**
     * Create the test index before tests run.
     */
    protected static function createIndex()
    {
        self::$index->create(['analysis' => ['analyzer' => MappingFactory::getCustomAnalyzers()]], true);
        $type = self::$index->getType('message');
        $mapping = (new MappingFactory())->create(EmailMessage::schema(), 'english');
        $mapping->setType($type);
        $mapping->send();
    }

    /**
     * Delete the test index after tests complete.
     */
    protected static function deleteIndex()
    {
        self::$index->delete();
    }

    public function setup()
    {
        if (null === self::$index) {
            $this->markTestSkipped('ELASTIC_HOST or ELASTIC_PORT was not supplied, skipping integration test.');
            return;
        }

        $this->marshaler = new DocumentMarshaler();
        $this->message = $this->createEmailMessage();
    }

    public function testAddDocument()
    {
        $type = self::$index->getType('message');
        $document = $this->marshaler->marshal($this->message);
        $document->setId(1);
        $type->addDocument($document);
        $this->assertTrue(true);
    }

    public function testGetDocument()
    {
        $type = self::$index->getType('message');
        $document = $type->getDocument(1);
        $message = $this->marshaler->unmarshal($document);
        $this->assertTrue($this->message->equals($message));
    }
}
