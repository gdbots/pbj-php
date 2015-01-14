<?php

namespace Gdbots\Pbj;

use Gdbots\Pbj\Exception\GdbotsPbjException;
use Gdbots\Pbj\Exception\RequiredFieldNotSetException;

interface Message
{
    /**
     * @return Schema
     */
    public static function schema();

    /**
     * Creates a new message
     *
     * @return static
     */
    public static function create();

    /**
     * Returns a new message from the provided array using the PhpArray Codec.
     * @see Gdbots\Pbj\Codec\PhpArray::decode
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data = []);

    /**
     * Returns the message as an associative array using the PhpArray Codec.
     * @see Gdbots\Pbj\Codec\PhpArray::encode
     *
     * @return array
     */
    public function toArray();

    /**
     * Encodes the message using the provided codec.
     *
     * @param Codec $codec
     * @return mixed
     */
    //public function encode(Codec $codec);

    /**
     * Decodes the message using the provided codec.
     *
     * @param Codec $codec
     * @return static
     *
     * @throws GdbotsPbjException
     * @throws RequiredFieldNotSetException
     */
    //public static function decode(Codec $codec);

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
     * Sets the value of a field.
     *
     * @param string $fieldName
     * @param mixed $value
     * @return static
     *
     * @throws GdbotsPbjException
     */
    public function set($fieldName, $value);

    /**
     * Clears the value of a field.
     *
     * @param string $fieldName
     * @return static
     *
     * @throws GdbotsPbjException
     * @throws RequiredFieldNotSetException
     */
    public function clear($fieldName);

    /**
     * Returns an array of field names that have been cleared.
     *
     * @return array
     */
    public function getClearedFields();

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
    public function addMap($fieldName, $key, $value);

    /**
     * Removes a key/value pair from a map.
     *
     * @param string $fieldName
     * @param string $key
     * @return static
     *
     * @throws \Exception
     */
    public function removeMap($fieldName, $key);
}