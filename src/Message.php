<?php
declare(strict_types=1);

namespace Gdbots\Pbj;

use Gdbots\Pbj\Exception\GdbotsPbjException;
use Gdbots\Pbj\Exception\LogicException;
use Gdbots\Pbj\Exception\RequiredFieldNotSet;
use Gdbots\Pbj\WellKnown\MessageRef;
use Gdbots\Pbj\WellKnown\NodeRef;

interface Message
{
    public static function schema(): Schema;

    /**
     * Creates a new message with the defaults populated.
     *
     * @return static
     */
    public static function create(): self;

    /**
     * Returns a new message from the provided array.
     *
     * @param array $data
     *
     * @return static
     *
     * @see \Gdbots\Pbj\Serializer\PhpArraySerializer::deserialize
     */
    public static function fromArray(array $data = []): self;

    /**
     * Returns the message as an associative array.
     *
     * @return array
     *
     * @see \Gdbots\Pbj\Serializer\PhpArraySerializer::serialize
     */
    public function toArray(): array;

    /**
     * Returns the message as a human readable string.
     *
     * @return string
     */
    public function __toString();

    /**
     * Generates an md5 hash of the json representation of the current message.
     *
     * @param string[] $ignoredFields
     *
     * @return string
     */
    public function generateEtag(array $ignoredFields = []): string;

    /**
     * Generates a MessageRef of the current message with an optional tag.
     *
     * @param string $tag
     *
     * @return MessageRef
     */
    public function generateMessageRef(?string $tag = null): MessageRef;

    /**
     * Generates a NodeRef of the current message.
     */
    public function generateNodeRef(): NodeRef;

    /**
     * Returns an array that can be used in a uri template to generate
     * a uri/url for this message.
     *
     * @link https://tools.ietf.org/html/rfc6570
     * @link https://github.com/gdbots/uri-template-php
     */
    public function getUriTemplateVars(): array;

    /**
     * Verifies all required fields have been populated and when
     * using strict mode will run the guard check on all fields.
     *
     * @param bool $strict
     * @param bool $recursive
     *
     * @return static
     *
     * @throws GdbotsPbjException
     * @throws RequiredFieldNotSet
     */
    public function validate(bool $strict = false, bool $recursive = false): self;

    /**
     * Freezes the message, making it immutable.  The message must be validated
     * before it can be frozen so this may throw an exception if some required
     * fields have not been populated. Using strict validation will also ensure
     * the value has been guarded by the field and type constraints.
     *
     * @param bool $withStrictValidation
     *
     * @return static
     *
     * @throws GdbotsPbjException
     * @throws RequiredFieldNotSet
     */
    public function freeze(bool $withStrictValidation = true): self;

    /**
     * Returns true if the message has been frozen.  A frozen message is
     * immutable and cannot be modified.
     *
     * @return bool
     */
    public function isFrozen(): bool;

    /**
     * Returns true if the data of the message matches.
     *
     * @param Message $other
     *
     * @return bool
     */
    public function equals(Message $other): bool;

    /**
     * Returns true if this message is being replayed.  Providing a value
     * will set the flag but this can only be done once.  Note that
     * setting a message as being "replayed" will also freeze the message.
     *
     * @param bool $replay
     *
     * @return bool
     *
     * @throws LogicException
     */
    public function isReplay(?bool $replay = null): bool;

    /**
     * Sets the value for a given field without doing any
     * encoding or validation (type guard). This should
     * only be used by serializers and codecs/marshalers.
     *
     * The internal value is optimized for php.
     *
     * @param string $fieldName
     * @param mixed  $value
     *
     * @return static
     *
     * @throws GdbotsPbjException
     *
     * @internal
     */
    public function setWithoutValidation(string $fieldName, $value): self;

    /**
     * Populates the defaults on all fields or just the fieldName provided.
     * Operation will NOT overwrite any fields already set.
     *
     * @param string $fieldName
     *
     * @return static
     */
    public function populateDefaults(?string $fieldName = null): self;

    /**
     * Merges the field values from the provided message into this message.
     * Only fields with matching field names and types will be copied.
     *
     * todo: review, possible optimized merge when schemas match
     *
     * @param Message $message
     *
     * @return static
     */
    //public function mergeFrom(Message $message);

    /**
     * Returns true if the field has been populated.
     *
     * @param string $fieldName
     *
     * @return bool
     */
    public function has(string $fieldName): bool;

    /**
     * Returns the value for the given field.  If the field has not
     * been set you will get a null value.
     *
     * @param string $fieldName
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $fieldName, $default = null);

    /**
     * A fast get method that returns the raw value instead of an
     * object when possible. E.g. a UuidType would normally return
     * a UuidIdentifier instance but fget just returns the string.
     *
     * Note: MessageType fields always return a Message instance.
     *
     * @param string $fieldName
     * @param mixed  $default
     *
     * @return mixed
     */
    public function fget(string $fieldName, $default = null);

    /**
     * Clears the value of a field.
     *
     * @param string $fieldName
     *
     * @return static
     *
     * @throws GdbotsPbjException
     */
    public function clear(string $fieldName): self;

    /**
     * Sets a single value field.
     *
     * @param string $fieldName
     * @param mixed  $value
     *
     * @return static
     *
     * @throws GdbotsPbjException
     */
    public function set(string $fieldName, $value): self;

    /**
     * Returns true if the provided value is in the set of values.
     *
     * @param string $fieldName
     * @param mixed  $value
     *
     * @return bool
     */
    public function isInSet(string $fieldName, $value): bool;

    /**
     * Adds an array of unique values to an unsorted set of values.
     *
     * @param string $fieldName
     * @param array  $values
     *
     * @return static
     *
     * @throws GdbotsPbjException
     */
    public function addToSet(string $fieldName, array $values): self;

    /**
     * Removes an array of values from a set.
     *
     * @param string $fieldName
     * @param array  $values
     *
     * @return static
     *
     * @throws GdbotsPbjException
     */
    public function removeFromSet(string $fieldName, array $values): self;

    /**
     * Returns true if the provided value is in the list of values.
     * This is a NOT a strict comparison, it uses "==".
     * @link http://php.net/manual/en/function.in-array.php
     *
     * @param string $fieldName
     * @param mixed  $value
     *
     * @return bool
     */
    public function isInList(string $fieldName, $value): bool;

    /**
     * Returns an item in a list or null if it doesn't exist.
     *
     * @param string $fieldName
     * @param int    $index
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getFromListAt(string $fieldName, int $index, $default = null);

    /**
     * Adds an array of values to an unsorted list/array (not unique).
     *
     * @param string $fieldName
     * @param array  $values
     *
     * @return static
     *
     * @throws GdbotsPbjException
     */
    public function addToList(string $fieldName, array $values): self;

    /**
     * Removes the element from the array at the index.
     *
     * @param string $fieldName
     * @param int    $index
     *
     * @return static
     *
     * @throws GdbotsPbjException
     */
    public function removeFromListAt(string $fieldName, int $index): self;

    /**
     * Returns true if the map contains the provided key.
     *
     * @param string $fieldName
     * @param string $key
     *
     * @return bool
     */
    public function isInMap(string $fieldName, string $key): bool;

    /**
     * Returns the value of a key in a map or null if it doesn't exist.
     *
     * @param string $fieldName
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getFromMap(string $fieldName, string $key, $default = null);

    /**
     * Adds a key/value pair to a map.
     *
     * @param string $fieldName
     * @param string $key
     * @param mixed  $value
     *
     * @return static
     *
     * @throws GdbotsPbjException
     */
    public function addToMap(string $fieldName, string $key, $value): self;

    /**
     * Removes a key/value pair from a map.
     *
     * @param string $fieldName
     * @param string $key
     *
     * @return static
     *
     * @throws GdbotsPbjException
     */
    public function removeFromMap(string $fieldName, string $key): self;
}
