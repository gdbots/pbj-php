<?php

namespace Gdbots\Messages\Type;

abstract class AbstractType implements Type
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

    /**
     * @see Type::getDefault
     */
    public function getDefault()
    {
        return null;
    }

    /**
     * @see Type::isScalar
     */
    public function isScalar()
    {
        return true;
    }

    /**
     * @see Type::isNumeric
     */
    public function isNumeric()
    {
        return false;
    }

    /**
     * @see Type::isString
     */
    public function isString()
    {
        return false;
    }
}