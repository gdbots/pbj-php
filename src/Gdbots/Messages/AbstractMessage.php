<?php

namespace Gdbots\Messages;

use Gdbots\Common\FromArray;
use Gdbots\Common\ToArray;

abstract class AbstractMessage implements Message, FromArray, ToArray, \JsonSerializable
{
    /**
     * @var Field[]
     */
    private static $fields;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @param array $data
     *
     * @throws \InvalidArgumentException
     */
    final private function __construct(array $data = array())
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @see Message::fields
     * @throws \LogicException
     */
    final public static function fields()
    {
        if (null === self::$fields) {
            $fields = static::getFields();
            foreach ($fields as $field) {
                if (isset(self::$fields[$field->getName()])) {
                    throw new \LogicException(sprintf('Field [%s] can only be defined once.', $field->getName()));
                }
                self::$fields[$field->getName()] = $field;
            }
        }

        return self::$fields;
    }

    /**
     * @return Field[]
     */
    protected static function getFields()
    {
        return [];
    }

    /**
     * @param string $name
     * @return Field
     * @throws \InvalidArgumentException
     */
    final public static function field($name)
    {
        self::fields();
        if (!isset(self::$fields[$name])) {
            throw new \InvalidArgumentException(sprintf('Field [%s] is not defined.', $name));
        }

        return self::$fields[$name];
    }

    /**
     * @param array $data
     * @return static
     */
    final public static function fromArray(array $data = array())
    {
        return new static($data);
    }

    /**
     * @return array
     *
     * @throws \LogicException
     */
    final public function toArray()
    {
        $payload = [];

        foreach (self::fields() as $field) {
            $payload[$field->getName()] = $this->get($field->getName());
        }

        return $payload;
    }

    /**
     * @return array
     */
    final public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param string $key
     * @return mixed
     */
    final protected function get($key)
    {
        // todo: handle nullable fields
        if (!isset($this->data[$key])) {
            return self::field($key)->getDefault();
        }

        return $this->data[$key];
    }

    /**
     * @param string $key
     * @param string $value
     * @return static
     *
     * @throws \Exception
     */
    final protected function set($key, $value)
    {
        $field = self::field($key);
        $field->guardValue($value);
        $this->data[$field->getName()] = $value;
        return $this;
    }
}