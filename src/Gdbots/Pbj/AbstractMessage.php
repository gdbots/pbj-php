<?php

namespace Gdbots\Pbj;

use Gdbots\Common\FromArray;
use Gdbots\Common\ToArray;
use Gdbots\Common\Util\ArrayUtils;
use Gdbots\Common\Util\StringUtils;
use Gdbots\Pbj\Codec\PhpArray;
use Gdbots\Pbj\Enum\FieldRule;
use Gdbots\Pbj\Exception\RequiredFieldNotSetException;

abstract class AbstractMessage implements Message, FromArray, ToArray, \JsonSerializable
{
    /**
     * An array of schemas per message type.
     * ['Fully\Qualified\ClassName' => [ array of Schema objects ]
     * @var array
     */
    private static $schemas = [];

    /**
     * @var array
     */
    private $data = [];

    /**
     * An array of fields that have been cleared or set to null that
     * must be included when serialized so it's clear that the
     * value has been unset.
     *
     * @var array
     */
    private $clearedFields = [];

    /**
     * @param array $data
     * @throws \Exception
     */
    final private function __construct(array $data = array())
    {
        $schema = static::schema();

        foreach ($data as $fieldName => $value) {
            if (!$schema->hasField($fieldName)) {
                // todo: review, what to do with unknown fields
                continue;
            }

            $field = $schema->getField($fieldName);

            switch ($field->getRule()->getValue()) {
                case FieldRule::A_SINGLE_VALUE:
                    $this->setSingleValue($fieldName, $field->decodeValue($value));
                    break;

                case FieldRule::A_SET:
                    Assertion::isArray($value, sprintf('Field [%s] must be an array.', $fieldName), $fieldName);
                    foreach ($value as $v) {
                        $this->addToSet($fieldName, [$field->decodeValue($v)]);
                    }
                    break;

                case FieldRule::A_LIST:
                    Assertion::isArray($value, sprintf('Field [%s] must be an array.', $fieldName), $fieldName);
                    foreach ($value as $v) {
                        $this->addToList($fieldName, [$field->decodeValue($v)]);
                    }
                    break;

                case FieldRule::A_MAP:
                    Assertion::true(ArrayUtils::isAssoc($value), sprintf('Field [%s] must be an associative array.', $fieldName), $fieldName);
                    foreach ($value as $k => $v) {
                        $this->addToMap($fieldName, $k, $field->decodeValue($v));
                    }
                    break;

                default:
                    break;
            }
        }

        foreach ($schema->getFields() as $field) {
            $this->populateDefault($field);
            if ($field->isRequired() && !$this->has($field->getName())) {
                throw new RequiredFieldNotSetException($this, $field);
            }
        }
    }

    /**
     * @param Field $field
     * @return static
     */
    final protected function populateDefault(Field $field)
    {
        if ($this->has($field->getName())) {
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
            $this->addToSet($field->getName(), $default);
            return $this;
        }

        $this->data[$field->getName()] = $default;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public static function schema()
    {
        $type = get_called_class();
        if (!isset(self::$schemas[$type])) {
            self::$schemas[$type] = static::defineSchema();
        }
        return self::$schemas[$type];
    }

    /**
     * @return Schema
     */
    protected static function defineSchema()
    {
        // by default an empty schema is created
        return Schema::create(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    final public static function create(array $data = [])
    {
        return new static($data);
    }

    /**
     * {@inheritdoc}
     */
    final public static function fromArray(array $data = [])
    {
        return new static($data);
    }

    /**
     * {@inheritdoc}
     */
    final public function toArray()
    {
        return PhpArray::create()->encode($this);
    }

    /**
     * {@inheritdoc}
     */
    final public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * {@inheritdoc}
     */
    final public function has($fieldName)
    {
        $schema = static::schema();
        if (!$schema->hasField($fieldName)) {
            return false;
        }

        $field = $schema->getField($fieldName);
        if ($field->isASingleValue()) {
            return isset($this->data[$field->getName()]);
        }

        return !empty($this->data[$field->getName()]);
    }

    /**
     * {@inheritdoc}
     */
    final public function get($fieldName)
    {
        if (!$this->has($fieldName)) {
            return null;
        }

        $field = static::schema()->getField($fieldName);
        if ($field->isASet()) {
            return array_values($this->data[$field->getName()]);
        }

        return $this->data[$field->getName()];
    }

    /**
     * {@inheritdoc}
     */
    final public function clear($fieldName)
    {
        $field = static::schema()->getField($fieldName);

        unset($this->data[$field->getName()]);
        $this->clearedFields[$field->getName()] = true;
        $this->populateDefault($field);

        if ($field->isRequired() && !$this->has($fieldName)) {
            throw new RequiredFieldNotSetException($this, $field);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function getClearedFields()
    {
        return array_keys($this->clearedFields);
    }

    /**
     * {@inheritdoc}
     */
    final public function setSingleValue($fieldName, $value)
    {
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isASingleValue(), sprintf('Field [%s] must be a single value.', $fieldName), $fieldName);

        if (null === $value) {
            return $this->clear($fieldName);
        }

        $field->guardValue($value);
        $this->data[$fieldName] = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function addToSet($fieldName, array $values)
    {
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isASet(), sprintf('Field [%s] must be a set.', $fieldName), $fieldName);

        foreach ($values as $value) {
            $field->guardValue($value);
            $key = strtolower(trim((string) $value));
            $this->data[$fieldName][$key] = $value;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function removeFromSet($fieldName, array $values)
    {
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isASet(), sprintf('Field [%s] must be a set.', $fieldName), $fieldName);

        foreach ($values as $value) {
            $key = strtolower(trim((string) $value));
            unset($this->data[$fieldName][$key]);
        }

        if ($field->isRequired() && !$this->has($fieldName)) {
            throw new RequiredFieldNotSetException($this, $field);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function addToList($fieldName, array $values)
    {
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isAList(), sprintf('Field [%s] must be a list.', $fieldName), $fieldName);

        foreach ($values as $value) {
            $field->guardValue($value);
            $this->data[$fieldName][] = $value;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function removeFromList($fieldName, array $values)
    {
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isAList(), sprintf('Field [%s] must be a list.', $fieldName), $fieldName);

        $values = array_diff((array)$this->data[$fieldName], $values);
        $this->data[$fieldName] = $values;

        if ($field->isRequired() && !$this->has($fieldName)) {
            throw new RequiredFieldNotSetException($this, $field);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function addToMap($fieldName, $key, $value)
    {
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isAMap(), sprintf('Field [%s] must be a map.', $fieldName), $fieldName);
        Assertion::string($key, sprintf('Field [%s] key [%s] must be a string.', $fieldName, StringUtils::varToString($key)), $fieldName);

        $field->guardValue($value);
        $this->data[$fieldName][$key] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function removeFromMap($fieldName, $key)
    {
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isAMap(), sprintf('Field [%s] must be a map.', $fieldName), $fieldName);
        Assertion::string($key, sprintf('Field [%s] key [%s] must be a string.', $fieldName, StringUtils::varToString($key)), $fieldName);

        unset($this->data[$fieldName][$key]);

        if ($field->isRequired() && !$this->has($fieldName)) {
            throw new RequiredFieldNotSetException($this, $field);
        }
    }
}