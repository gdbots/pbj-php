<?php

namespace Gdbots\Pbj\Serializer;

use Gdbots\Pbj\Decoder;
use Gdbots\Pbj\Encoder;
use Gdbots\Pbj\Exception\GdbotsPbjException;
use Gdbots\Pbj\Message;

interface Serializer extends Encoder, Decoder
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
     *
     * @throws \Exception
     * @throws GdbotsPbjException
     */
    public function deserialize($data, array $options = []);
}
