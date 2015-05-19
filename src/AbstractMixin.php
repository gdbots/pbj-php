<?php

namespace Gdbots\Pbj;

use Gdbots\Common\ToArray;

abstract class AbstractMixin implements Mixin, ToArray, \JsonSerializable
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
    final public function toArray()
    {
        return [
            'id' => $this->getId(),
            'fields' => $this->getFields(),
        ];
    }

    /**
     * @return array
     */
    final public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return string
     */
    final public function __toString()
    {
        return $this->getId()->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return [];
    }
}
