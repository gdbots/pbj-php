<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Serializer;

use Gdbots\Pbj\Message;

interface Serializer
{
    public function serialize(Message $message, array $options = []): mixed;

    public function deserialize(mixed $data, array $options = []): Message;
}
