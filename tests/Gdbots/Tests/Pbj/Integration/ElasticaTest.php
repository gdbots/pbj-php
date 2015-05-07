<?php

namespace Gdbots\Tests\Pbj\Integration;

use Elastica\Client;
use Elastica\Index;
use Gdbots\Pbj\Marshaler\Elastica\DocumentMarshaler;
use Gdbots\Pbj\Marshaler\Elastica\MappingFactory;
use Gdbots\Pbj\Message;
use Gdbots\Tests\Pbj\FixtureLoader;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;
use Gdbots\Tests\Pbj\Fixtures\MapsMessage;
use Gdbots\Tests\Pbj\Fixtures\NestedMessage;

class ElasticaTest extends \PHPUnit_Framework_TestCase
{
    use FixtureLoader;

    /** @var Client */
    protected $client;

    /** @var MappingFactory */
    protected $mappingFactory;

    /** @var DocumentMarshaler */
    protected $marshaler;

    /** @var EmailMessage */
    private $message;

    /** @var string */
    private $indexName;

    /** @var Index */
    private $index;

    public function setup()
    {
        $host = getenv('ELASTICA_HOST');
        $port = getenv('ELASTICA_PORT') ?: 9200;
        $this->indexName = getenv('ELASTICA_INDEX') ?: 'pbj_tests';

        if (empty($host) || empty($port)) {
            $this->markTestSkipped('ELASTICA_HOST or ELASTICA_PORT was not supplied, skipping integration test.');
            return;
        }

        // auto register's their schemas
        EmailMessage::schema();
        NestedMessage::schema();
        MapsMessage::schema();

        $this->client = new Client(['connections' => [['host' => $host, 'port' => $port]]]);
        $this->mappingFactory = new MappingFactory();
        $this->marshaler = new DocumentMarshaler();
        $this->message = $this->createEmailMessage();
        $this->index = $this->client->getIndex($this->indexName);
    }

    public function testMapping()
    {
        $this->index->create(
            [
                'analysis' => [
                    'analyzer' => MappingFactory::getCustomAnalyzers()
                ]
            ],
            true
        );

        $type = $this->index->getType('message');
        $mapping = $this->mappingFactory->create(EmailMessage::schema());
        $mapping->setType($type)->send();
    }

    public function testAddDocument()
    {
        $type = $this->index->getType('message');

        $document = $this->marshaler->marshal($this->message);
        $document->setId(1);

        //echo 'MARSHAL TO ES DOCUMENT TEST: ' . PHP_EOL;
        //echo json_encode($document->toArray(), JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;

        $type->addDocument($document);
    }

    public function testGetDocument()
    {
        $type = $this->index->getType('message');

        $document = $type->getDocument(1);

        /** @var Message $message */
        $message = $this->marshaler->unmarshal($document);
        $this->assertTrue($this->message->equals($message));

        //echo 'MESSAGE CREATED FROM DOCUMENT: ' . PHP_EOL;
        //echo $message;
    }
}
