<?php

namespace Gdbots\Pbj\Codec;

use Gdbots\Pbj\Codec;

abstract class AbstractCodec implements Codec
{
    private static $instances = [];

    /**
     * private constructor to ensure flyweight construction.
     */
    final private function __construct() {}

    /**
     * @return static
     */
    final public static function create()
    {
        $type = get_called_class();
        if (!isset(self::$instances[$type])) {
            self::$instances[$type] = new static();
        }
        return self::$instances[$type];
    }
}