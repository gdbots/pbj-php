<?php

namespace Gdbots\Pbj\Serializer;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Message;

class PhpSerializer implements Serializer
{
    /**
     * {@inheritdoc}
     */
    public function serialize(Message $message, array $options = [])
    {
        return serialize($message);
    }

    /**
     * {@inheritdoc}
     *
     * @return Message
     */
    public function deserialize($data, array $options = [])
    {
        /** @var Message $message */
        $message = unserialize($data);
        Assertion::isInstanceOf($message, 'Gdbots\Pbj\Message');
        return $message;
    }
}
