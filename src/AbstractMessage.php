<?php

namespace Gdbots\Pbj;

use Gdbots\Common\FromArray;
use Gdbots\Common\ToArray;
use Gdbots\Pbj\Exception\FrozenMessageIsImmutable;
use Gdbots\Pbj\Exception\LogicException;
use Gdbots\Pbj\Exception\SchemaNotDefined;
use Gdbots\Pbj\Serializer\PhpArraySerializer;
use Gdbots\Pbj\Serializer\YamlSerializer;
use Gdbots\Pbj\Exception\RequiredFieldNotSet;

abstract class AbstractMessage implements Message, FromArray, ToArray, \JsonSerializable
{
    /**
     * An array of schemas per message type.
     * ['Fully\Qualified\ClassName' => [ array of Schema objects ]
     * @var array
     */
    private static $schemas = [];

    /** @var PhpArraySerializer */
    private static $serializer;

    /** @var YamlSerializer */
    private static $yamlSerializer;

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
     * @see Message::freeze
     * @var bool
     */
    private $isFrozen = false;

    /**
     * @see Message::isReplay
     * @var bool
     */
    private $isReplay;

    /**
     * Nothing fancy on new messages... we let the serializers or application code get fancy.
     */
    final public function __construct() {}

    /**
     * {@inheritdoc}
     * @return Schema
     */
    final public static function schema()
    {
        $type = get_called_class();
        if (!isset(self::$schemas[$type])) {
            $schema = static::defineSchema();

            if (!$schema instanceof Schema) {
                throw new SchemaNotDefined(
                    sprintf('Message [%s] must return a Schema from the defineSchema method.', $type)
                );
            }

            if ($schema->getClassName() !== $type) {
                throw new SchemaNotDefined(
                    sprintf(
                        'Schema [%s] returned from defineSchema must be for class [%s], not [%s]',
                        $schema->getId()->toString(),
                        $type,
                        $schema->getClassName()
                    )
                );
            }
            self::$schemas[$type] = $schema;
        }
        return self::$schemas[$type];
    }

    /**
     * @return Schema
     * @throws SchemaNotDefined
     */
    protected static function defineSchema()
    {
        throw new SchemaNotDefined(
            sprintf('Message [%s] must return a Schema from the defineSchema method.', get_called_class())
        );
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public static function create()
    {
        /** @var Message $message */
        $message = new static();
        return $message->populateDefaults();
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public static function fromArray(array $data = [])
    {
        if (null === self::$serializer) {
            self::$serializer = new PhpArraySerializer();
        }
        $message = self::$serializer->deserialize($data);
        return $message;
    }

    /**
     * {@inheritdoc}
     */
    final public function toArray()
    {
        if (null === self::$serializer) {
            self::$serializer = new PhpArraySerializer();
        }
        return self::$serializer->serialize($this);
    }

    /**
     * Returns a Yaml string version of the message.
     * Useful for debugging or logging.
     *
     * @return string
     */
    final public function __toString()
    {
        try {
            if (null === self::$yamlSerializer) {
                self::$yamlSerializer = new YamlSerializer();
            }
            return self::$yamlSerializer->serialize($this);
        } catch (\Exception $e) {
            return sprintf(
                'Failed to render [%s] as a string with error: %s',
                self::schema()->toString(),
                $e->getMessage()
            );
        }
    }

    /**
     * @return array
     */
    final public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return static
     */
    final public function __clone()
    {
        $this->data = unserialize(serialize($this->data));
        $this->unFreeze();
    }

    /**
     * {@inheritdoc}
     */
    final public function generateEtag(array $ignoredFields = [])
    {
        if (empty($ignoredFields)) {
            return md5(json_encode($this));
        }

        $array = $this->toArray();
        foreach ($ignoredFields as $field) {
            unset($array[$field]);
        }
        return md5(json_encode($array));
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function validate()
    {
        foreach (static::schema()->getRequiredFields() as $field) {
            if (!$this->has($field->getName())) {
                throw new RequiredFieldNotSet($this, $field);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function freeze()
    {
        if ($this->isFrozen()) {
            return $this;
        }

        $this->validate();
        $this->isFrozen = true;

        foreach (static::schema()->getFields() as $field) {
            if ($field->getType()->isMessage()) {
                /** @var self $value */
                $value = $this->get($field->getName());
                if (empty($value)) {
                    continue;
                }

                if ($value instanceof Message) {
                    $value->freeze();
                    continue;
                }

                /** @var self $v */
                foreach ($value as $v) {
                    $v->freeze();
                }
            }
        }

        return $this;
    }

    /**
     * Recursively unfreezes this object and any of its children.
     * Used internally during the clone process.
     */
    private function unFreeze()
    {
        $this->isFrozen = false;
        $this->isReplay = null;

        foreach (static::schema()->getFields() as $field) {
            if ($field->getType()->isMessage()) {
                /** @var self $value */
                $value = $this->get($field->getName());
                if (empty($value)) {
                    continue;
                }

                if ($value instanceof Message) {
                    $value->unFreeze();
                    continue;
                }

                /** @var self $v */
                foreach ($value as $v) {
                    $v->unFreeze();
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    final public function isFrozen()
    {
        return $this->isFrozen;
    }

    /**
     * Ensures a frozen message can't be modified.
     * @throws FrozenMessageIsImmutable
     */
    private function guardFrozenMessage()
    {
        if ($this->isFrozen) {
            throw new FrozenMessageIsImmutable($this);
        }
    }

    /**
     * {@inheritdoc}
     * This could probably use some work.  :)  low level serialization string match.
     */
    public function equals(Message $other)
    {
        return json_encode($this) === json_encode($other);
    }

    /**
     * {@inheritdoc}
     */
    final public function isReplay($replay = null)
    {
        if (null === $replay) {
            if (null === $this->isReplay) {
                $this->isReplay = false;
            }
            return $this->isReplay;
        }

        if (null === $this->isReplay) {
            $this->isReplay = (bool) $replay;
            if ($this->isReplay) {
                $this->freeze();
            }
            return $this->isReplay;
        }

        throw new LogicException('You can only set the replay mode on one time.');
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function populateDefaults($fieldName = null)
    {
        $this->guardFrozenMessage();

        if (!empty($fieldName)) {
            $this->populateDefault(static::schema()->getField($fieldName));
            return $this;
        }

        foreach (static::schema()->getFields() as $field) {
            $this->populateDefault($field);
        }

        return $this;
    }

    /**
     * Populates the default on a single field if it's not already set
     * and the default generated is not a null value or empty array.
     *
     * @param Field $field
     * @return bool Returns true if a non null/empty default was applied or already present.
     */
    private function populateDefault(Field $field)
    {
        if ($this->has($field->getName())) {
            return true;
        }

        $default = $field->getDefault($this);
        if (null === $default) {
            return false;
        }

        if ($field->isASingleValue()) {
            $this->data[$field->getName()] = $default;
            unset($this->clearedFields[$field->getName()]);
            return true;
        }

        if (empty($default)) {
            return false;
        }

        /*
         * sets have a special handling to deal with unique values
         */
        if ($field->isASet()) {
            $this->addToSet($field->getName(), $default);
            return true;
        }

        $this->data[$field->getName()] = $default;
        unset($this->clearedFields[$field->getName()]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    final public function has($fieldName)
    {
        if (!isset($this->data[$fieldName])) {
            return false;
        }

        if (is_array($this->data[$fieldName])) {
            return !empty($this->data[$fieldName]);
        }

        return true;
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
            return array_values($this->data[$fieldName]);
        }

        return $this->data[$fieldName];
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function clear($fieldName)
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        unset($this->data[$fieldName]);
        $this->clearedFields[$fieldName] = true;
        $this->populateDefault($field);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function hasClearedField($fieldName)
    {
        return isset($this->clearedFields[$fieldName]);
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
     * @return static
     */
    final public function setSingleValue($fieldName, $value)
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isASingleValue(), sprintf('Field [%s] must be a single value.', $fieldName), $fieldName);

        if (null === $value) {
            return $this->clear($fieldName);
        }

        $field->guardValue($value);
        $this->data[$fieldName] = $value;
        unset($this->clearedFields[$fieldName]);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function isInSet($fieldName, $value)
    {
        if (empty($this->data[$fieldName]) || !is_array($this->data[$fieldName])) {
            return false;
        }

        if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
            $key = trim((string) $value);
        } else {
            return false;
        }

        if (0 === strlen($key)) {
            return false;
        }

        return isset($this->data[$fieldName][strtolower($key)]);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function addToSet($fieldName, array $values)
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isASet(), sprintf('Field [%s] must be a set.', $fieldName), $fieldName);

        foreach ($values as $value) {
            if (0 === strlen($value)) {
                continue;
            }
            $field->guardValue($value);
            $key = strtolower(trim((string) $value));
            $this->data[$fieldName][$key] = $value;
        }

        if (!empty($this->data[$fieldName])) {
            unset($this->clearedFields[$fieldName]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function removeFromSet($fieldName, array $values)
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isASet(), sprintf('Field [%s] must be a set.', $fieldName), $fieldName);

        foreach ($values as $value) {
            if (0 === strlen($value)) {
                continue;
            }
            $key = strtolower(trim((string) $value));
            unset($this->data[$fieldName][$key]);
        }

        if (empty($this->data[$fieldName])) {
            $this->clearedFields[$fieldName] = true;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function isInList($fieldName, $value)
    {
        if (empty($this->data[$fieldName]) || !is_array($this->data[$fieldName])) {
            return false;
        }

        return in_array($value, $this->data[$fieldName]);
    }

    /**
     * {@inheritdoc}
     */
    final public function getFromListAt($fieldName, $index)
    {
        $index = (int) $index;
        if (empty($this->data[$fieldName])
            || !is_array($this->data[$fieldName])
            || !isset($this->data[$fieldName][$index])
        ) {
            return null;
        }
        return $this->data[$fieldName][$index];
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function addToList($fieldName, array $values)
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isAList(), sprintf('Field [%s] must be a list.', $fieldName), $fieldName);

        foreach ($values as $value) {
            $field->guardValue($value);
            $this->data[$fieldName][] = $value;
        }

        unset($this->clearedFields[$fieldName]);
        return $this;
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function removeFromListAt($fieldName, $index)
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isAList(), sprintf('Field [%s] must be a list.', $fieldName), $fieldName);
        $index = (int) $index;

        if (empty($this->data[$fieldName])) {
            return $this;
        }

        array_splice($this->data[$fieldName], $index, 1);
        if (empty($this->data[$fieldName])) {
            $this->clearedFields[$fieldName] = true;
            return $this;
        }

        // reset the numerical indexes
        // todo: review, does this need to be optimized?
        $this->data[$fieldName] = array_values($this->data[$fieldName]);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function isInMap($fieldName, $key)
    {
        if (empty($this->data[$fieldName]) || !is_array($this->data[$fieldName]) || !is_string($key)) {
            return false;
        }
        return isset($this->data[$fieldName][$key]);
    }

    /**
     * {@inheritdoc}
     */
    final public function getFromMap($fieldName, $key)
    {
        if (!$this->isInMap($fieldName, $key)) {
            return null;
        }
        return $this->data[$fieldName][$key];
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function addToMap($fieldName, $key, $value)
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isAMap(), sprintf('Field [%s] must be a map.', $fieldName), $fieldName);
        Assertion::string($key, sprintf('Field [%s] key must be a string.', $fieldName), $fieldName);

        if (null === $value) {
            return $this->removeFromMap($fieldName, $key);
        }

        $field->guardValue($value);
        $this->data[$fieldName][$key] = $value;
        unset($this->clearedFields[$fieldName]);

        return $this;
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function removeFromMap($fieldName, $key)
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isAMap(), sprintf('Field [%s] must be a map.', $fieldName), $fieldName);
        Assertion::string($key, sprintf('Field [%s] key must be a string.', $fieldName), $fieldName);

        unset($this->data[$fieldName][$key]);

        if (empty($this->data[$fieldName])) {
            $this->clearedFields[$fieldName] = true;
        }

        return $this;
    }
}
