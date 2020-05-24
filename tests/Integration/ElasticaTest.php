<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj\Integration;

use Elastica\Client;
use Elastica\Index;
use Gdbots\Pbj\Marshaler\Elastica\DocumentMarshaler;
use Gdbots\Pbj\Marshaler\Elastica\MappingBuilder;
use Gdbots\Tests\Pbj\FixtureLoader;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;
use PHPUnit\Framework\TestCase;

class ElasticaTest extends TestCase
{
    use FixtureLoader;

    protected static ?Index $index = null;
    protected ?DocumentMarshaler $marshaler = null;
    protected ?EmailMessage $message = null;

    public static function setUpBeforeClass(): void
    {
        $host = getenv('ELASTIC_HOST');
        $port = getenv('ELASTIC_PORT') ?: 9200;
        $indexName = getenv('ELASTIC_INDEX') ?: 'pbj_tests';

        if (empty($host) || empty($port)) {
            return;
        }

        $transport = '443' === $port ? 'https' : 'http';

        $client = new Client(['transport' => $transport, 'host' => $host, 'port' => $port]);
        self::$index = $client->getIndex($indexName);
        self::createIndex();
    }

    public static function tearDownAfterClass(): void
    {
        if (null === self::$index) {
            return;
        }

        self::deleteIndex();
    }

    /**
     * Create the test index before tests run.
     */
    protected static function createIndex(): void
    {
        self::$index->create([
            'settings' => [
                'analysis' => [
                    'analyzer'   => MappingBuilder::getCustomAnalyzers(),
                    'normalizer' => MappingBuilder::getCustomNormalizers(),
                ],
            ]], true);
        $mapping = (new MappingBuilder())->addSchema(EmailMessage::schema())->build();
        $mapping->send(self::$index);
    }

    /**
     * Delete the test index after tests complete.
     */
    protected static function deleteIndex(): void
    {
        self::$index->delete();
    }

    public function setUp(): void
    {
        if (null === self::$index) {
            $this->markTestSkipped('ELASTIC_HOST or ELASTIC_PORT was not supplied, skipping integration test.');
            return;
        }

        $this->marshaler = new DocumentMarshaler();
        $this->message = $this->createEmailMessage();
    }

    public function testAddDocument(): void
    {
        $document = $this->marshaler->marshal($this->message);
        $document->setId('1')->setParam('__type', 'message');
        self::$index->addDocument($document);
        $this->assertTrue(true);
    }

    public function testGetDocument(): void
    {
        $document = self::$index->getDocument('1');
        $message = $this->marshaler->unmarshal($document);
        $this->assertTrue($this->message->equals($message));
    }
}
