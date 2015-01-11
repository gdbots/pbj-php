<?php

namespace Gdbots\Messages;

use Gdbots\Common\FromArray;
use Gdbots\Common\ToArray;
use Gdbots\Common\Util\ArrayUtils;
use Gdbots\Common\Util\StringUtils;
use Gdbots\Messages\Enum\FieldRule;

abstract class AbstractMessage implements Message, FromArray, ToArray, \JsonSerializable
{
    /**
     * An array of fields per message type.
     * ['Fully\Qualified\ClassName' => [ array of Field objects ]
     * @var array
     */
    private static $fields = [];

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
            $field = static::field($name);

            switch ($field->getRule()->getValue()) {
                case FieldRule::A_SINGLE_VALUE:
                    $this->setSingleValue($name, $field->decodeValue($value));
                    break;

                case FieldRule::A_SET:
                    Assertion::isArray($value, sprintf('Field [%s] must be an array.', $name), $name);
                    foreach ($value as $v) {
                        $this->addValuesToSet($name, [$field->decodeValue($v)]);
                    }
                    break;

                case FieldRule::A_LIST:
                    Assertion::isArray($value, sprintf('Field [%s] must be an array.', $name), $name);
                    foreach ($value as $v) {
                        $this->addValuesToList($name, [$field->decodeValue($v)]);
                    }
                    break;

                case FieldRule::A_MAP:
                    Assertion::true(ArrayUtils::isAssoc($value), sprintf('Field [%s] must be an associative array.', $name), $name);
                    foreach ($value as $k => $v) {
                        $this->addValueToMap($name, $k, $field->decodeValue($v));
                    }
                    break;

                default:
                    break;
            }
        }

        foreach (static::fields() as $field) {
            $this->populateDefault($field);
            if ($field->isRequired() && !$this->has($field)) {
                throw new \LogicException(sprintf('Field [%s] is required.', $field->getName()));
            }
        }
    }

    /**
     * @param Field $field
     * @return static
     */
    final protected function populateDefault(Field $field)
    {
        if ($this->has($field)) {
            return $this;
        }

        $default = $field->getDefault($this);
        if (null === $default) {
            return $this;
        }

        if ($field->isASingleValue()) {
            $this->data[$field->getName()] = $default;
            return $this;
        }

        if (empty($default)) {
            return $this;
        }

        /*
         * sets have a special handling to deal with unique values
         */
        if ($field->isASet()) {
            $this->addValuesToSet($field->getName(), $default);
            return $this;
        }

        $this->data[$field->getName()] = $default;
        return $this;
    }

    /**
     * @see Message::fields
     */
    final public static function fields()
    {
        $type = get_called_class();
        if (!isset(self::$fields[$type])) {
            $fields = static::getFields();
            foreach ($fields as $field) {
                if (isset(self::$fields[$type][$field->getName()])) {
                    throw new \LogicException(sprintf('Field [%s] can only be defined once.', $field->getName()));
                }
                self::$fields[$type][$field->getName()] = $field;
            }
        }

        return self::$fields[$type];
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
        $fields = static::fields();
        if (!isset($fields[$name])) {
            throw new \InvalidArgumentException(sprintf('Field [%s] is not defined.', $name));
        }

        return $fields[$name];
    }

    /**
     * @param array $data
     * @return static
     */
    final public static function create(array $data = [])
    {
        return new static($data);
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

        foreach (static::fields() as $field) {
            if (!$this->has($field)) {
                continue;
            }

            $name = $field->getName();
            switch ($field->getRule()->getValue()) {
                case FieldRule::A_SINGLE_VALUE:
                    $payload[$name] = $field->encodeValue($this->data[$name]);
                    break;

                case FieldRule::A_SET:
                    $payload[$name] = array_map(function($value) use ($field) {
                            return $field->encodeValue($value);
                        }, array_values($this->data[$name]));
                    break;

                case FieldRule::A_LIST:
                    $payload[$name] = array_map(function($value) use ($field) {
                            return $field->encodeValue($value);
                        }, $this->data[$name]);
                    break;

                case FieldRule::A_MAP:
                    $payload[$name] = [];
                    foreach ($this->data[$name] as $key => $value) {
                        $payload[$name][$key] = $field->encodeValue($value);
                    }
                    break;

                default:
                    break;
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
     * @param Field|string $nameOrField
     * @return bool
     */
    final public function has($nameOrField)
    {
        $field = $nameOrField instanceof Field ? $nameOrField : static::field($nameOrField);
        if ($field->isASingleValue()) {
            return isset($this->data[$field->getName()]);
        }
        return !empty($this->data[$field->getName()]);
    }

    /**
     * @param Field|string $nameOrField
     * @return mixed
     */
    final public function get($nameOrField)
    {
        $field = $nameOrField instanceof Field ? $nameOrField : static::field($nameOrField);

        if (!$this->has($field)) {
            return $field->isASingleValue() ? $field->getDefault($this) : [];
        }

        if ($field->isASet()) {
            return array_values($this->data[$field->getName()]);
        }

        return $this->data[$field->getName()];
    }

    /**
     * Clears the value of a field and restores the default if available.  Clearing a
     * required field that has no default will throw an exception.
     *
     * @param Field|string $nameOrField
     * @return static
     *
     * @throws \LogicException
     */
    final protected function clear($nameOrField)
    {
        $field = $nameOrField instanceof Field ? $nameOrField : static::field($nameOrField);
        unset($this->data[$field->getName()]);
        $this->populateDefault($field);

        if ($field->isRequired() && !$this->has($field)) {
            throw new \LogicException(sprintf('Field [%s] is required.', $field->getName()));
        }

        return $this;
    }

    /**
     * Sets a single value field.
     *
     * @param string $name
     * @param mixed $value
     * @return static
     *
     * @throws \Exception
     */
    final protected function setSingleValue($name, $value)
    {
        $field = static::field($name);
        Assertion::true($field->isASingleValue(), sprintf('Field [%s] must be a single value.', $name), $name);

        if (null === $value) {
            return $this->clear($name);
        }

        $field->guardValue($value);
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * Adds an array of unique values to an unsorted set of values.
     *
     * @param string $name
     * @param array $values
     * @return static
     *
     * @throws \Exception
     */
    final protected function addValuesToSet($name, array $values)
    {
        $field = static::field($name);
        Assertion::true($field->isASet(), sprintf('Field [%s] must be a set.', $name), $name);

        foreach ($values as $value) {
            $field->guardValue($value);
            $key = strtolower(trim((string) $value));
            $this->data[$name][$key] = $value;
        }

        return $this;
    }

    /**
     * Removes an array of values from a set.
     *
     * @param string $name
     * @param array $values
     * @return static
     *
     * @throws \Exception
     */
    final protected function removeValuesFromSet($name, array $values)
    {
        $field = static::field($name);
        Assertion::true($field->isASet(), sprintf('Field [%s] must be a set.', $name), $name);

        foreach ($values as $value) {
            $key = strtolower(trim((string) $value));
            unset($this->data[$name][$key]);
        }

        if ($field->isRequired() && !$this->has($field)) {
            throw new \LogicException(sprintf('Field [%s] is required but you have removed all of the values from the set.', $name));
        }

        return $this;
    }

    /**
     * Adds an array of values to an unsorted list/array (not unique).
     *
     * @param string $name
     * @param array $values
     * @return static
     *
     * @throws \Exception
     */
    final protected function addValuesToList($name, array $values)
    {
        $field = static::field($name);
        Assertion::true($field->isAList(), sprintf('Field [%s] must be a list.', $name), $name);

        foreach ($values as $value) {
            $field->guardValue($value);
            $this->data[$name][] = $value;
        }

        return $this;
    }

    /**
     * Removes an array of values from a list.
     *
     * @param string $name
     * @param array $values
     * @return static
     *
     * @throws \Exception
     */
    final protected function removeValuesFromList($name, array $values)
    {
        $field = static::field($name);
        Assertion::true($field->isAList(), sprintf('Field [%s] must be a list.', $name), $name);

        $values = array_diff((array)$this->data[$name], $values);
        $this->data[$name] = $values;

        if ($field->isRequired() && !$this->has($field)) {
            throw new \LogicException(sprintf('Field [%s] is required but you have removed all of the values from the list.', $name));
        }

        return $this;
    }

    /**
     * Adds a key/value pair to a map.
     *
     * @param string $name
     * @param string $key
     * @param mixed $value
     * @return static
     *
     * @throws \Exception
     */
    final protected function addValueToMap($name, $key, $value)
    {
        $field = static::field($name);
        Assertion::true($field->isAMap(), sprintf('Field [%s] must be a map.', $name), $name);
        Assertion::string($key, sprintf('Field [%s] key [%s] must be a string.', $name, StringUtils::varToString($key)), $name);

        $field->guardValue($value);
        $this->data[$name][$key] = $value;

        return $this;
    }

    /**
     * Removes a key/value pair from a map.
     *
     * @param string $name
     * @param string $key
     * @return static
     *
     * @throws \Exception
     */
    final protected function removeValueFromMap($name, $key)
    {
        $field = static::field($name);
        Assertion::true($field->isAMap(), sprintf('Field [%s] must be a map.', $name), $name);
        Assertion::string($key, sprintf('Field [%s] key [%s] must be a string.', $name, StringUtils::varToString($key)), $name);

        unset($this->data[$name][$key]);

        if ($field->isRequired() && !$this->has($field)) {
            throw new \LogicException(sprintf('Field [%s] is required but you have removed all of the key/value pairs from the map.', $name));
        }
    }
}