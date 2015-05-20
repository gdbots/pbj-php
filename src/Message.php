<?php

namespace Gdbots\Pbj;

use Gdbots\Pbj\Exception\GdbotsPbjException;
use Gdbots\Pbj\Exception\LogicException;
use Gdbots\Pbj\Exception\SchemaNotDefined;
use Gdbots\Pbj\Exception\RequiredFieldNotSet;

interface Message
{
    /**
     * @return Schema
     * @throws SchemaNotDefined
     */
    public static function schema();

    /**
     * Creates a new message with the defaults populated.
     *
     * @return static
     */
    public static function create();

    /**
     * Returns a new message from the provided array using the PhpArray Serializer.
     * @see Gdbots\Pbj\Serializer\PhpArraySerializer::deserialize
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data = []);

    /**
     * Returns the message as an associative array using the PhpArray Serializer.
     * @see Gdbots\Pbj\Serializer\PhpArraySerializer::serialize
     *
     * @return array
     */
    public function toArray();

    /**
     * Generates an md5 hash of the json representation of the current message.
     *
     * @return string
     */
    public function generateEtag();

    /**
     * Verifies all required fields have been populated.
     *
     * @return static
     *
     * @throws GdbotsPbjException
     * @throws RequiredFieldNotSet
     */
    public function validate();

    /**
     * Freezes the message, making it immutable.  The message must be valid
     * before it can be frozen so this may throw an exception if some required
     * fields have not been populated.
     *
     * @return static
     *
     * @throws GdbotsPbjException
     * @throws RequiredFieldNotSet
     */
    public function freeze();

    /**
     * Returns true if the message has been frozen.  A frozen message is
     * immutable and cannot be modified.
     *
     * @return bool
     */
    public function isFrozen();

    /**
     * Returns true if the data of the message matches.
     *
     * @param Message $other
     * @return bool
     */
    public function equals(Message $other);

    /**
     * Returns true if this message is being replayed.  Providing a value
     * will set the flag but this can only be done once.  Note that
     * setting a message as being "replayed" will also freeze the message.
     *
     * @param bool|null $replay
     * @return bool
     *
     * @throws LogicException
     */
    public function isReplay($replay = null);

    /**
     * Populates the defaults on all fields or just the fieldName provided.
     * Operation will NOT overwrite any fields already set.
     *
     * @param string|null $fieldName
     * @return static
     */
    public function populateDefaults($fieldName = null);

    /**
     * Merges the field values from the provided message into this message.
     * Only fields with matching field names and types will be copied.
     *
     * todo: review, possible optimized merge when schemas match
     *
     * @param Message $message
     * @return static
     */
    //public function mergeFrom(Message $message);

    /**
     * Returns true if the field has been populated.
     *
     * @param string $fieldName
     * @return bool
     */
    public function has($fieldName);

    /**
     * Returns the value for the given field.  If the field has not
     * been set you will get a null value.
     *
     * @param string $fieldName
     * @return mixed
     */
    public function get($fieldName);

    /**
     * Clears the value of a field.
     *
     * @param string $fieldName
     * @return static
     *
     * @throws GdbotsPbjException
     * @throws RequiredFieldNotSet
     */
    public function clear($fieldName);

    /**
     * Returns true if the field has been cleared.
     *
     * @param string $fieldName
     * @return bool
     */
    public function hasClearedField($fieldName);

    /**
     * Returns an array of field names that have been cleared.
     *
     * @return array
     */
    public function getClearedFields();

    /**
     * Sets a single value field.
     *
     * @param string $fieldName
     * @param mixed $value
     * @return static
     *
     * @throws GdbotsPbjException
     */
    public function setSingleValue($fieldName, $value);

    /**
     * Returns true if the provided value is in the set of values.
     *
     * @param string $fieldName
     * @param mixed $value
     * @return bool
     */
    public function isInSet($fieldName, $value);

    /**
     * Adds an array of unique values to an unsorted set of values.
     *
     * @param string $fieldName
     * @param array $values
     * @return static
     *
     * @throws GdbotsPbjException
     */
    public function addToSet($fieldName, array $values);

    /**
     * Removes an array of values from a set.
     *
     * @param string $fieldName
     * @param array $values
     * @return static
     *
     * @throws GdbotsPbjException
     */
    public function removeFromSet($fieldName, array $values);

    /**
     * Returns true if the provided value is in the list of values.
     * This is a NOT a strict comparison, it uses "==".
     * @link http://php.net/manual/en/function.in-array.php
     *
     * @param string $fieldName
     * @param mixed $value
     * @return bool
     */
    public function isInList($fieldName, $value);

    /**
     * Returns an item in a list or null if it doesn't exist.
     *
     * @param string $fieldName
     * @param int $index
     * @return mixed
     */
    public function getFromListAt($fieldName, $index);

    /**
     * Adds an array of values to an unsorted list/array (not unique).
     *
     * @param string $fieldName
     * @param array $values
     * @return static
     *
     * @throws GdbotsPbjException
     */
    public function addToList($fieldName, array $values);

    /**
     * Removes the element from the array at the index.
     *
     * @param string $fieldName
     * @param int $index
     * @return static
     *
     * @throws GdbotsPbjException
     */
    public function removeFromListAt($fieldName, $index);

    /**
     * Returns true if the map contains the provided key.
     *
     * @param string $fieldName
     * @param string $key
     * @return bool
     */
    public function isInMap($fieldName, $key);

    /**
     * Returns the value of a key in a map or null if it doesn't exist.
     *
     * @param string $fieldName
     * @param string $key
     * @return mixed
     */
    public function getFromMap($fieldName, $key);

    /**
     * Adds a key/value pair to a map.
     *
     * @param string $fieldName
     * @param string $key
     * @param mixed $value
     * @return static
     *
     * @throws GdbotsPbjException
     */
    public function addToMap($fieldName, $key, $value);

    /**
     * Removes a key/value pair from a map.
     *
     * @param string $fieldName
     * @param string $key
     * @return static
     *
     * @throws GdbotsPbjException
     */
    public function removeFromMap($fieldName, $key);
}