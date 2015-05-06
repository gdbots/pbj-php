<?php

namespace Gdbots\Tests\Pbj\Marshaler\DynamoDb;

use Gdbots\Pbj\Marshaler\DynamoDb\ItemMarshaler;
use Gdbots\Pbj\Serializer\JsonSerializer;
use Gdbots\Pbj\Serializer\Serializer;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;

class ItemMarshalerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ItemMarshaler */
    protected $marshaler;

    /** @var Serializer */
    protected $serializer;

    public function setup()
    {
        $this->marshaler = new ItemMarshaler();
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
        $result = $this->marshaler->marshal($message);

        echo json_encode($result, JSON_PRETTY_PRINT);
    }
}
