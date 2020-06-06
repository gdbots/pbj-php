<?php
declare(strict_types=1);

namespace Gdbots\Pbj;

use Gdbots\Pbj\Exception\FrozenMessageIsImmutable;
use Gdbots\Pbj\Exception\LogicException;
use Gdbots\Pbj\Exception\RequiredFieldNotSet;
use Gdbots\Pbj\Serializer\PhpArraySerializer;
use Gdbots\Pbj\WellKnown\MessageRef;
use Gdbots\Pbj\WellKnown\NodeRef;

abstract class AbstractMessage implements Message, \JsonSerializable
{
    private static ?PhpArraySerializer $serializer = null;
    protected array $data = [];
    protected array $decoded = [];

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
        if (!isset($data[Schema::PBJ_FIELD_NAME])) {
            $data[Schema::PBJ_FIELD_NAME] = static::schema()->getId()->toString();
        }

        return self::getSerializer()->deserialize($data);
    }

    final public function toArray(): array
    {
        return self::getSerializer()->serialize($this);
    }

    final public function __toString()
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

    final public function jsonSerialize()
    {
        return $this->toArray();
    }

    final public function __sleep()
    {
        return ['data'];
    }

    final public function __wakeup()
    {
        $this->decoded = [];
        $this->isFrozen = false;
        $this->isReplay = null;
    }

    final public function __clone()
    {
        $this->data = unserialize(serialize($this->data));
        $this->decoded = [];
        $this->isFrozen = false;
        $this->isReplay = null;
    }

    final public function generateEtag(array $ignoredFields = []): string
    {
        $array = $this->toArray();

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
        return new MessageRef(static::schema()->getCurie(), 'null', $tag);
    }

    public function generateNodeRef(): NodeRef
    {
        return NodeRef::fromNode($this);
    }

    public function getUriTemplateVars(): array
    {
        return [];
    }

    final public function validate(bool $strict = false, bool $recursive = false): self
    {
        if (!$strict && $this->isFrozen()) {
            return $this;
        }

        if (!$strict) {
            foreach (static::schema()->getRequiredFields() as $field) {
                if (!$this->has($field->getName())) {
                    throw new RequiredFieldNotSet($this, $field);
                }
            }
        } else {
            foreach (static::schema()->getFields() as $field) {
                if ($field->isRequired() && !$this->has($field->getName())) {
                    throw new RequiredFieldNotSet($this, $field);
                }

                // just getting the field will decode/guard the values
                $this->get($field->getName());
            }
        }

        if (!$recursive) {
            return $this;
        }

        foreach ($this->getNestedMessages() as $message) {
            $message->validate($strict, $recursive);
        }

        return $this;
    }

    final public function freeze(bool $withStrictValidation = true): self
    {
        if ($this->isFrozen()) {
            return $this;
        }

        $this->validate($withStrictValidation);
        $this->isFrozen = true;

        foreach ($this->getNestedMessages() as $message) {
            $message->freeze($withStrictValidation);
        }

        return $this;
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

    final public function populateDefaults(?string $fieldName = null): self
    {
        $this->guardFrozenMessage();

        if ($fieldName) {
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
        $fieldName = $field->getName();

        if ($this->has($fieldName)) {
            return true;
        }

        $default = $field->getDefault($this);
        if (null === $default) {
            return false;
        }

        if ($field->isASingleValue()) {
            $this->decoded[$fieldName] = $default;
            $this->data[$fieldName] = $this->encodeValue($default, $field);
            return true;
        }

        if (empty($default)) {
            return false;
        }

        /*
         * sets have a special handling to deal with unique values
         */
        if ($field->isASet()) {
            $this->addToSet($fieldName, $default);
            return true;
        }

        $this->decoded[$fieldName] = $default;
        $this->data[$fieldName] = $this->encodeValue($default, $field);
        return true;
    }

    final public function setWithoutValidation(string $fieldName, $value): self
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);

        if (null === $value) {
            return $this->clear($fieldName);
        }

        unset($this->decoded[$fieldName]);

        if ($field->isASet()) {
            $this->data[$fieldName] = [];
            foreach ($value as $v) {
                $this->data[$fieldName][strtolower(trim((string)$v))] = $v;
            }
            return $this;
        }

        $this->data[$fieldName] = $value;
        return $this;
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
        if ($this->hasDecoded($fieldName)) {
            return $field->isASet() ? array_values($this->decoded[$fieldName]) : $this->decoded[$fieldName];
        }

        if ($field->isASingleValue()) {
            $decoded = $this->decodeValue($this->data[$fieldName], $field);
        } else {
            $decoded = array_map(function ($value) use ($fieldName, $field) {
                return $this->decodeValue($value, $field);
            }, $this->data[$fieldName]);
        }

        $this->decoded[$fieldName] = $decoded;
        return $field->isASet() ? array_values($this->decoded[$fieldName]) : $this->decoded[$fieldName];
    }

    final public function fget(string $fieldName, $default = null)
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
        unset($this->decoded[$fieldName]);
        unset($this->data[$fieldName]);
        $this->populateDefault($field);
        return $this;
    }

    final public function set(string $fieldName, $value): self
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isASingleValue(), 'Field must be a single value.', $fieldName);

        if (null === $value) {
            return $this->clear($fieldName);
        }

        $field->guardValue($value);
        $this->decoded[$fieldName] = $value;
        $this->data[$fieldName] = $this->encodeValue($value, $field);
        return $this;
    }

    final public function isInSet(string $fieldName, $value): bool
    {
        if (!$this->has($fieldName)) {
            return false;
        }

        return isset($this->data[$fieldName][strtolower(trim((string)$value))]);
    }

    final public function addToSet(string $fieldName, array $values): self
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isASet(), 'Field must be a set.', $fieldName);

        foreach ($values as $value) {
            if (0 === strlen((string)$value)) {
                continue;
            }

            $field->guardValue($value);
            $key = strtolower(trim((string)$value));
            $this->decoded[$fieldName][$key] = $value;
            $this->data[$fieldName][$key] = $this->encodeValue($value, $field);
        }

        return $this;
    }

    final public function removeFromSet(string $fieldName, array $values): self
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isASet(), 'Field must be a set.', $fieldName);

        foreach ($values as $value) {
            if (0 === strlen($value)) {
                continue;
            }

            $key = strtolower(trim((string)$value));
            unset($this->decoded[$fieldName][$key]);
            unset($this->data[$fieldName][$key]);
        }

        return $this;
    }

    final public function isInList(string $fieldName, $value): bool
    {
        if (!$this->has($fieldName)) {
            return false;
        }

        return in_array($value, $this->get($fieldName));
    }

    final public function getFromListAt(string $fieldName, int $index, $default = null)
    {
        if (!$this->has($fieldName)) {
            return $default;
        }

        $values = $this->get($fieldName);
        return $values[$index] ?? $default;
    }

    final public function addToList(string $fieldName, array $values): self
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isAList(), 'Field must be a list.', $fieldName);

        foreach ($values as $value) {
            $field->guardValue($value);
            $this->decoded[$fieldName][] = $value;
            $this->data[$fieldName][] = $this->encodeValue($value, $field);
        }

        return $this;
    }

    final public function removeFromListAt(string $fieldName, int $index): self
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isAList(), 'Field must be a list.', $fieldName);

        if (empty($this->data[$fieldName])) {
            return $this;
        }

        unset($this->decoded[$fieldName]);
        array_splice($this->data[$fieldName], $index, 1);
        if (empty($this->data[$fieldName])) {
            return $this;
        }

        // reset the numerical indexes
        $this->data[$fieldName] = array_values($this->data[$fieldName]);
        return $this;
    }

    final public function isInMap(string $fieldName, string $key): bool
    {
        if (!$this->has($fieldName)) {
            return false;
        }

        return isset($this->data[$fieldName][$key]);
    }

    final public function getFromMap(string $fieldName, string $key, $default = null)
    {
        if (!$this->isInMap($fieldName, $key)) {
            return $default;
        }

        $values = $this->get($fieldName);
        return $values[$key] ?? $default;
    }

    final public function addToMap(string $fieldName, string $key, $value): self
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isAMap(), 'Field must be a map.', $fieldName);

        if (null === $value) {
            return $this->removeFromMap($fieldName, $key);
        }

        $field->guardValue($value);
        $this->decoded[$fieldName][$key] = $value;
        $this->data[$fieldName][$key] = $this->encodeValue($value, $field);

        return $this;
    }

    final public function removeFromMap(string $fieldName, string $key): self
    {
        $this->guardFrozenMessage();
        $field = static::schema()->getField($fieldName);
        Assertion::true($field->isAMap(), 'Field must be a map.', $fieldName);

        unset($this->decoded[$fieldName][$key]);
        unset($this->data[$fieldName][$key]);
        return $this;
    }

    private static function getSerializer(): PhpArraySerializer
    {
        if (null === self::$serializer) {
            self::$serializer = new PhpArraySerializer();
        }

        return self::$serializer;
    }

    private function hasDecoded(string $fieldName): bool
    {
        return isset($this->decoded[$fieldName]);
    }

    private function encodeValue($value, Field $field)
    {
        $type = $field->getType();
        if ($type->isMessage()) {
            return $value;
        }

        return $type->encode($value, $field, self::getSerializer());
    }

    private function decodeValue($value, Field $field)
    {
        $decoded = $field->getType()->decode($value, $field, self::getSerializer());
        $field->guardValue($decoded);
        return $decoded;
    }

    /**
     * @return self[]
     */
    private function getNestedMessages(): array
    {
        $messages = [];
        foreach (static::schema()->getFields() as $field) {
            if ($field->getType()->isMessage()) {
                /** @var self $value */
                $value = $this->fget($field->getName());
                if (empty($value)) {
                    continue;
                }

                if ($value instanceof self) {
                    $messages[] = $value;
                    continue;
                }

                /** @var self $v */
                foreach ($value as $v) {
                    $messages[] = $v;
                }
            }
        }

        return $messages;
    }
}
