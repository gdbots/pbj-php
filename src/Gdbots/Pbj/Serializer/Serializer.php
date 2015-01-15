<?php

namespace Gdbots\Pbj\Serializer;

use Gdbots\Pbj\Message;

interface Serializer
{
    /**
     * @param Message $message
     * @param array $options
     * @return mixed
     */
    public function serialize(Message $message, array $options = []);

    /**
     * @param mixed $data
     * @param array $options
     * @return Message
     */
    public function deserialize($data, array $options = []);
}
