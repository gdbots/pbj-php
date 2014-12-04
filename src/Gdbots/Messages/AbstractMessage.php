<?php

namespace Gdbots\Messages;

use Assert\Assertion;
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
        foreach ($data as $name => $value) {
            $field = self::field($name);

            if ($field->isASingleValue()) {
                $this->set($name, $field->getType()->decode($value, $field));

            } elseif ($field->isASet()) {
                Assertion::isArray($value, sprintf('Field [%s] must be an array.', $name), $name);
                foreach ($value as $v) {
                    $this->addToSet($name, $field->getType()->decode($v, $field));
                }

            } elseif ($field->isAMap()) {

            }
        }

        foreach (self::fields() as $field) {
            if (!$field->isRequired()) {
                continue;
            }

            if (!$this->has($field->getName())) {
                if (!$field->hasDefault()) {
                    throw new \LogicException(sprintf('Field [%s] is required.', $field->getName()));
                }
                $this->data[$field->getName()] = $field->getDefault();
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
            if ($field->isASingleValue()) {
                if (!array_key_exists($field->getName(), $this->data)) {
                    if (!$field->hasDefault()) {
                        continue;
                    }
                    $value = $field->getDefault();
                } else {
                    $value = $this->data[$field->getName()];
                }
                $payload[$field->getName()] = $field->getType()->encode($value, $field);

            } elseif ($field->isASet()) {
                if (!$this->has($field->getName()) || empty($this->data[$field->getName()])) {
                    continue;
                }

                $payload[$field->getName()] = array_map(function($value) use ($field) {
                        return $field->getType()->encode($value, $field);
                    }, array_values($this->data[$field->getName()]));

            } elseif ($field->isAMap()) {

            }
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
     * @param string $name
     * @return bool
     */
    final protected function has($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param string $name
     * @return mixed
     */
    final protected function get($name)
    {
        if (!isset($this->data[$name])) {
            return self::field($name)->getDefault();
        }

        return $this->data[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return static
     *
     * @throws \Exception
     */
    final protected function set($name, $value)
    {
        $field = self::field($name);
        Assertion::true(
                $field->isASingleValue(),
                sprintf('Field [%s] is not a single value and cannot use $this->set().', $name),
                $name
            );

        $field->guardValue($value);
        $this->data[$field->getName()] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return static
     *
     * @throws \Exception
     */
    final protected function addToSet($name, $value)
    {
        $field = self::field($name);
        Assertion::true(
                $field->isASet(),
                sprintf('Field [%s] is not a set and cannot use $this->addToSet().', $name),
                $name
            );

        if (!$this->has($name)) {
            $this->data[$field->getName()] = [];
        }

        $field->guardValue($value);
        $key = strtolower(trim($value));
        $this->data[$field->getName()][$key] = $value;
        return $this;
    }
}