<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj\Marshaler\Elastica;

use Elastica\Document;
use Gdbots\Pbj\Marshaler\Elastica\DocumentMarshaler;
use Gdbots\Tests\Pbj\FixtureLoader;
use PHPUnit\Framework\TestCase;

class DocumentMarshalerTest extends TestCase
{
    use FixtureLoader;

    protected DocumentMarshaler $marshaler;

    public function setup(): void
    {
        $this->marshaler = new DocumentMarshaler();
    }

    public function testMarshal()
    {
        $message = $this->createEmailMessage();
        $document = $this->marshaler->marshal($message);
        $this->assertInstanceOf(Document::class, $document);
        $message2 = $this->marshaler->unmarshal($document);
        $this->assertTrue($message->equals($message2));
    }
}
