<?php

namespace Gdbots\Pbj;

abstract class AbstractMixin implements Mixin
{
    private static $instances = [];

    /**
     * Private constructor to ensure flyweight construction.
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
    public function getFields()
    {
        return [];
    }
}
