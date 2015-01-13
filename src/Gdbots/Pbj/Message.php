<?php

namespace Gdbots\Pbj;

use Gdbots\Pbj\Exception\FieldAlreadyDefinedException;
use Gdbots\Pbj\Exception\FieldNotDefinedException;
use Gdbots\Pbj\Exception\RequiredFieldNotSetException;

interface Message
{
    /**
     * @return Schema
     */
    public static function schema();






    /**
     * @return Field[]
     * @throws FieldAlreadyDefinedException
     * @throws \Exception
     */
    public static function fields();

    /**
     * @param string $fieldName
     * @return bool
     */
    public static function hasField($fieldName);

    /**
     * @param string $fieldName
     * @return Field
     * @throws FieldNotDefinedException
     */
    public static function field($fieldName);






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
     * @throws RequiredFieldNotSetException
     * @throws \Exception
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
     * Clears the value of a field.
     *
     * @param string $fieldName
     * @return static
     * @throws RequiredFieldNotSetException
     */
    public function clear($fieldName);
}