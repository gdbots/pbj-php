<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Enum\TypeName;
use Gdbots\Pbj\Exception\DecodeValueException;
use Gdbots\Pbj\Exception\GdbotsPbjException;
use Gdbots\Pbj\Field;

interface Type
{
    /**
     * @return Type
     */
    public static function create();

    /**
     * @return TypeName
     */
    public function getTypeName();

    /**
     * @param mixed $value
     * @param Field $field
     * @throws \Exception
     */
    public function guard($value, Field $field);

    /**
     * @param mixed $value
     * @param Field $field
     * @return mixed
     */
    public function encode($value, Field $field);

    /**
     * @param mixed $value
     * @param Field $field
     * @return mixed
     * @throws GdbotsPbjException
     * @throws DecodeValueException
     */
    public function decode($value, Field $field);

    /**
     * Returns true if the value gets decoded and stored during runtime as a scalar value.
     *
     * @return bool
     */
    public function decodesToScalar();

    /**
     * Returns true if the value gets encoded to a scalar value.  This is important to
     * know because a big int, date, enum, etc. is stored as an object on the message
     * but when the message is encoded to an array, json, etc. it's scalar value.
     *
     * @return bool
     */
    public function encodesToScalar();

    /**
     * @return mixed
     */
    public function getDefault();

    /**
     * @return bool
     */
    public function isBoolean();

    /**
     * @return bool
     */
    public function isNumeric();

    /**
     * @return bool
     */
    public function isString();

    /**
     * Returns the minimum value supported by an integer type.
     * @return int
     */
    public function getMin();

    /**
     * Returns the maximum value supported by an integer type.
     * @return int
     */
    public function getMax();

    /**
     * Returns the maximum number of bytes supported by the string or binary type.
     * @return int
     */
    public function getMaxBytes();
}