<?php

namespace Gdbots\Tests\Pbj\Marshaler\Elastica;

use Gdbots\Pbj\Marshaler\Elastica\DocumentMarshaler;
use Gdbots\Pbj\Serializer\JsonSerializer;
use Gdbots\Pbj\Serializer\Serializer;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;

class DocumentMarshalerTest extends \PHPUnit_Framework_TestCase
{
    /** @var DocumentMarshaler */
    protected $marshaler;

    /** @var Serializer */
    protected $serializer;

    public function setup()
    {
        $this->marshaler = new DocumentMarshaler();
        $this->serializer = new JsonSerializer();
    }

    /**
     * @return EmailMessage
     */
    private function createEmailMessage()
    {
        $json = file_get_contents(__DIR__ . '/../../Fixtures/email-message.json');
        // auto registers the schema with the MessageResolver
        // only done for tests or dynamic messages.
        EmailMessage::schema();
        return $this->serializer->deserialize($json);
    }

    public function testMarshal()
    {
        $message = $this->createEmailMessage();
        $document = $this->marshaler->marshal($message);
        $document
            ->setId($message->getMessageId())
            ->setTimestamp($message->getMicrotimeSent()->toString());

        $this->assertInstanceOf('Elastica\Document', $document);

        $message2 = $this->marshaler->unmarshal($document);

        $this->assertTrue($message->equals($message2));

        //echo json_encode($document->toArray(), JSON_PRETTY_PRINT);
        //echo json_encode($message2, JSON_PRETTY_PRINT);
    }
}
