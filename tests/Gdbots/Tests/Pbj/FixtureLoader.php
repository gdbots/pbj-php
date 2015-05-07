<?php

namespace Gdbots\Tests\Pbj;

use Gdbots\Pbj\Serializer\JsonSerializer;
use Gdbots\Pbj\Serializer\Serializer;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;
use Gdbots\Tests\Pbj\Fixtures\MapsMessage;
use Gdbots\Tests\Pbj\Fixtures\NestedMessage;

trait FixtureLoader
{
    /** @var Serializer */
    protected $serializer;

    /**
     * @return Serializer
     */
    protected function getSerializer()
    {
        if (null === $this->serializer) {
            $this->serializer = new JsonSerializer();
        }
        return $this->serializer;
    }

    /**
     * @return EmailMessage
     */
    protected function createEmailMessage()
    {
        $json = file_get_contents(__DIR__ . '/Fixtures/email-message.json');
        // auto registers the schema with the MessageResolver
        // only done for tests or dynamic messages.
        EmailMessage::schema();
        NestedMessage::schema();
        MapsMessage::schema();
        return $this->getSerializer()->deserialize($json);
    }
}
