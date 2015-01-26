<?php

namespace Gdbots\Pbj\Serializer;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Message;
use Gdbots\Pbj\MessageResolver;
use Gdbots\Pbj\SchemaId;

abstract class AbstractSerializer implements Serializer
{
    /**
     * @param string $schemaId
     * @return Message
     */
    protected function createMessage($schemaId)
    {
        $schemaId = SchemaId::fromString($schemaId);
        $className = MessageResolver::resolveSchemaId($schemaId);

        /** @var Message $message */
        $message = new $className();
        Assertion::implementsInterface($message, 'Gdbots\Pbj\Message');

        if ($message::schema()->getId() !== $schemaId) {
        }

        // todo: assert message schema matches schemaid

        return $message;
    }
}
