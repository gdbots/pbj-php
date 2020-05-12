<?php

namespace Gdbots\Pbj;

use Gdbots\Common\FromArray;
use Gdbots\Common\ToArray;
use Gdbots\Pbj\Exception\FrozenMessageIsImmutable;
use Gdbots\Pbj\Exception\LogicException;
use Gdbots\Pbj\Exception\RequiredFieldNotSet;
use Gdbots\Pbj\Serializer\PhpArraySerializer;

abstract class AbstractMessage implements Message, FromArray, ToArray, \JsonSerializable
{
    private static ?PhpArraySerializer $serializer = null;
    private array $data = [];

    /**
     * An array of fields that have been cleared or set to null that
     * must be included when serialized so it's clear that the
     * value has been unset.
     */
    private array $clearedFields = [];

    /** @see Message::freeze */
    private bool $isFrozen = false;

    /** @see Message::isReplay */
    private ?bool $isReplay = null;

    /**
     * Nothing fancy on new messages... we let the serializers or application code get fancy.
     */
    final public function __construct()
    {
    }

    final public static function schema(): Schema
    {
        static $schema;

        if (null === $schema) {
            $schema = static::defineSchema();
        }

        return $schema;
    }

    abstract protected static function defineSchema(): Schema;

    final public static function create(): self
    {
        return (new static())->populateDefaults();
    }

    final public static function fromArray(array $data = []): self
    {
        if (null === self::$serializer) {
            self::$serializer = new PhpArraySerializer();
        }

        if (!isset($data[Schema::PBJ_FIELD_NAME])) {
            $data[Schema::PBJ_FIELD_NAME] = static::schema()->getId()->toString();
        }

        return self::$serializer->deserialize($data);
    }

    final public function toArray(): array
    {
        if (null === self::$serializer) {
            self::$serializer = new PhpArraySerializer();
        }
        return self::$serializer->serialize($this);
    }

    final public function __toString()
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

    final public function jsonSerialize()
    {
        return $this->toArray();
    }

    final public function __clone()
    {
        $this->data = unserialize(serialize($this->data));
        $this->unFreeze();
    }

    /**
     * {@inheritdoc}
     * todo: review performance
     */
    final public function generateEtag(array $ignoredFields = []): string
    {
        if (null === self::$serializer) {
            self::$serializer = new PhpArraySerializer();
        }

        $array = self::$serializer->serialize($this);

        if (empty($ignoredFields)) {
            return md5(json_encode($array));
        }

        foreach ($ignoredFields as $field) {
            unset($array[$field]);
        }

        return md5(json_encode($array));
    }

    public function generateMessageRef(?string $tag = null): MessageRef
    {
        return new MessageRef(static::schema()->getCurie(), null, $tag);
    }

    public function getUriTemplateVars(): array
    {
        return [];
    }

    /**
     * todo: recursively validate nested messages?
     */
    final public function validate(): self
    {
        foreach (static::schema()->getRequiredFields() as $field) {
            if (!$this->has($field->getName())) {
                throw new RequiredFieldNotSet($this, $field);
            }
        }

        return $this;
    }

    final public function freeze(): self
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
    private function unFreeze(): void
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

    final public function isFrozen(): bool
    {
        return $this->isFrozen;
    }

    /**
     * Ensures a frozen message can't be modified.
     *
     * @throws FrozenMessageIsImmutable
     */
    private function guardFrozenMessage(): void
    {
        if ($this->isFrozen) {
            throw new FrozenMessageIsImmutable($this);
        }
    }

    /**
     * {@inheritdoc}
     * This could probably use some work.  :)  low level serialization string match.
     */
    public function equals(Message $other): bool
    {
        return json_encode($this) === json_encode($other);
    }

    final public function isReplay(?bool $replay = null): bool
    {
        if (null === $replay) {
            if (null === $this->isReplay) {
                $this->isReplay = false;
            }
            return $this->isReplay;
        }

        if (null === $this->isReplay) {
            $this->isReplay = (bool)$replay;
            if ($this->isReplay) {
                $this->freeze();
            }
            return $this->isReplay;
        }

        throw new LogicException('You can only set the replay mode on one time.');
    }

    final public function populateDefaults(string $fieldName = null): self
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
     *
     * @return bool Returns true if a non null/empty default was applied or already present.
     */
    private function populateDefault(Field $field): bool
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

    final public function has(string $fieldName): bool
    {
        if (!isset($this->data[$fieldName])) {
            return false;
        }

        if (is_array($this->data[$fieldName])) {
            return !empty($this->data[$fieldName]);
        }

        return true;
    }

    final public function get(string $fieldName, $default = null)
    {
        if (!$this->has($fieldName)) {
            return $default;
        }

        $field = static::schema()->getField($fieldName);
        if ($field->isASet()) {
            return array_values($this->data[$fieldName]);
        }

        return $this->data[$fieldName];
    }

    final public function clear(string $fieldName): self
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        unset($this->data[$fieldName]);
        $this->clearedFields[$fieldName] = true;
        $this->populateDefault($field);
        return $this;
    }

    final public function hasClearedField(string $fieldName): bool
    {
        return isset($this->clearedFields[$fieldName]);
    }

    final public function getClearedFields(): array
    {
        return array_keys($this->clearedFields);
    }

    final public function set(string $fieldName, $value): self
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

    final public function isInSet(string $fieldName, $value): bool
    {
        if (empty($this->data[$fieldName]) || !is_array($this->data[$fieldName])) {
            return false;
        }

        if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
            $key = trim((string)$value);
        } else {
            return false;
        }

        if (0 === strlen($key)) {
            return false;
        }

        return isset($this->data[$fieldName][strtolower($key)]);
    }

    final public function addToSet(string $fieldName, array $values): self
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isASet(), sprintf('Field [%s] must be a set.', $fieldName), $fieldName);

        foreach ($values as $value) {
            if (0 === strlen($value)) {
                continue;
            }
            $field->guardValue($value);
            $key = strtolower(trim((string)$value));
            $this->data[$fieldName][$key] = $value;
        }

        if (!empty($this->data[$fieldName])) {
            unset($this->clearedFields[$fieldName]);
        }

        return $this;
    }

    final public function removeFromSet(string $fieldName, array $values): self
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isASet(), sprintf('Field [%s] must be a set.', $fieldName), $fieldName);

        foreach ($values as $value) {
            if (0 === strlen($value)) {
                continue;
            }
            $key = strtolower(trim((string)$value));
            unset($this->data[$fieldName][$key]);
        }

        if (empty($this->data[$fieldName])) {
            $this->clearedFields[$fieldName] = true;
        }

        return $this;
    }

    final public function isInList(string $fieldName, $value): bool
    {
        if (empty($this->data[$fieldName]) || !is_array($this->data[$fieldName])) {
            return false;
        }

        return in_array($value, $this->data[$fieldName]);
    }

    final public function getFromListAt(string $fieldName, int $index, $default = null)
    {
        $index = (int)$index;
        if (empty($this->data[$fieldName])
            || !is_array($this->data[$fieldName])
            || !isset($this->data[$fieldName][$index])
        ) {
            return $default;
        }
        return $this->data[$fieldName][$index];
    }

    final public function addToList(string $fieldName, array $values): self
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

    final public function removeFromListAt(string $fieldName, int $index): self
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isAList(), sprintf('Field [%s] must be a list.', $fieldName), $fieldName);
        $index = (int)$index;

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

    final public function isInMap(string $fieldName, string $key): bool
    {
        if (empty($this->data[$fieldName]) || !is_array($this->data[$fieldName]) || !is_string($key)) {
            return false;
        }
        return isset($this->data[$fieldName][$key]);
    }

    final public function getFromMap(string $fieldName, string $key, $default = null)
    {
        if (!$this->isInMap($fieldName, $key)) {
            return $default;
        }

        return $this->data[$fieldName][$key];
    }

    final public function addToMap(string $fieldName, string $key, $value): self
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isAMap(), sprintf('Field [%s] must be a map.', $fieldName), $fieldName);

        if (null === $value) {
            return $this->removeFromMap($fieldName, $key);
        }

        $field->guardValue($value);
        $this->data[$fieldName][$key] = $value;
        unset($this->clearedFields[$fieldName]);

        return $this;
    }

    final public function removeFromMap(string $fieldName, string $key): self
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isAMap(), sprintf('Field [%s] must be a map.', $fieldName), $fieldName);

        unset($this->data[$fieldName][$key]);

        if (empty($this->data[$fieldName])) {
            $this->clearedFields[$fieldName] = true;
        }

        return $this;
    }

    public function __wakeup()
    {
        $this->isFrozen = false;
        $this->isReplay = null;
        $this->clearedFields = [];
    }
}
