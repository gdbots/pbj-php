<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Enum\TypeName;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Exception\EncodeValueFailed;
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
     * Shortcut to returning the value of the TypeName
     * @return string
     */
    public function getTypeValue();

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
     *
     * @throws GdbotsPbjException
     * @throws EncodeValueFailed
     */
    public function encode($value, Field $field);

    /**
     * @param mixed $value
     * @param Field $field
     * @return mixed
     *
     * @throws GdbotsPbjException
     * @throws DecodeValueFailed
     */
    public function decode($value, Field $field);

    /**
     * Returns true if the value gets decoded and stored during runtime as a scalar value.
     *
     * @return bool
     */
    public function isScalar();

    /**
     * Returns true if the value gets encoded to a scalar value.  This is important to
     * know because a big int, date, enum, etc. is stored as an object on the message
     * but when the message is encoded to an array, json, etc. it's a scalar value.
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
    public function isBinary();

    /**
     * @return bool
     */
    public function isNumeric();

    /**
     * @return bool
     */
    public function isString();

    /**
     * @return bool
     */
    public function isMessage();

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

    /**
     * @return bool
     */
    public function allowedInSet();
}