<?php

namespace Gdbots\Pbj;

interface Codec
{
    /**
     * @return Codec
     */
    public static function create();

    /**
     * @param Message $message
     * @param bool $includeAllFields
     * @return mixed
     */
    public function encode(Message $message, $includeAllFields = false);

    /**
     * @param mixed $data
     * @return Message
     */
    public function decode($data);
}
