<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Serializer;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Message;

class PhpSerializer implements Serializer
{
    public function serialize(Message $message, array $options = [])
    {
        return serialize($message);
    }

    public function deserialize($data, array $options = []): Message
    {
        $message = unserialize($data);
        Assertion::isInstanceOf($message, Message::class);
        return $message;
    }
}
