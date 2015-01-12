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
     * {@inheritdoc}
     */
    public function getDefault()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function isScalar()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isBoolean()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isNumeric()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isString()
    {
        return false;
    }
}