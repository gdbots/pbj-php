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
     * @throws \Exception
     */
    final private function __construct(array $data = array())
    {
        foreach ($data as $key => $value) {
            $field = self::field($key);
            $this->set($key, $field->getType()->decode($value, $field));
        }

        foreach (self::fields() as $field) {
            if (!$field->isRequired()) {
                continue;
            }

            if (!$this->has($field->getName())) {
                $this->set($field->getName(), $field->getDefault());
                throw new \LogicException(sprintf('Field [%s] is required.', $field->getName()));
            }
        }
    }

    /**
     * @see Message::fields
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
     * @see Message::field
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
    final public static function fromArray(array $data = [])
    {
        return new static($data);
    }

    /**
     * @return array
     */
    final public function toArray()
    {
        $payload = [];

        foreach (self::fields() as $field) {
            if (!array_key_exists($field->getName(), $this->data)) {
                if (!$field->hasDefault()) {
                    continue;
                }
                $value = $field->getDefault();
            } else {
                $value = $this->data[$field->getName()];
            }
            $payload[$field->getName()] = $field->getType()->encode($value, $field);
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
     * @return bool
     */
    final protected function has($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * @param string $key
     * @return mixed
     */
    final protected function get($key)
    {
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