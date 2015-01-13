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
     * @param Message $message
     * @param mixed $data
     */
    public function decode(Message $message, $data);
}
