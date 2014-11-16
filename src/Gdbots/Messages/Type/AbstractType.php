<?php

namespace Gdbots\Messages\Type;

abstract class AbstractType implements Type
{
    private static $instance;

    /**
     * private constructor to ensure flyweight construction.
     */
    final private function __construct() {}

    /**
     * @return static
     */
    final public static function create()
    {
        if (null === self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}