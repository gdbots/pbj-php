<?php

namespace Gdbots\Tests\Pbj\Marshaler\Elastica;

use Gdbots\Pbj\Marshaler\Elastica\DocumentMarshaler;
use Gdbots\Tests\Pbj\FixtureLoader;

class DocumentMarshalerTest extends \PHPUnit_Framework_TestCase
{
    use FixtureLoader;

    /** @var DocumentMarshaler */
    protected $marshaler;

    public function setup()
    {
        $this->marshaler = new DocumentMarshaler();
    }

    public function testMarshal()
    {
        $message = $this->createEmailMessage();
        $document = $this->marshaler->marshal($message);
        $this->assertInstanceOf('Elastica\Document', $document);
        $message2 = $this->marshaler->unmarshal($document);
        $this->assertTrue($message->equals($message2));
    }
}
