<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Enum\TypeName;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Exception\EncodeValueFailed;
use Gdbots\Pbj\Exception\GdbotsPbjException;
use Gdbots\Pbj\Field;

interface Type
{
    public static function create(): self;

    public function getTypeName(): TypeName;

    /**
     * Shortcut to returning the value of the TypeName
     *
     * @return string
     */
    public function getTypeValue(): string;

    /**
     * @param mixed $value
     * @param Field $field
     *
     * @throws \Throwable
     */
    public function guard($value, Field $field): void;

    /**
     * @param mixed $value
     * @param Field $field
     * @param Codec $codec
     *
     * @return mixed
     *
     * @throws GdbotsPbjException
     * @throws EncodeValueFailed
     */
    public function encode($value, Field $field, ?Codec $codec = null);

    /**
     * @param mixed $value
     * @param Field $field
     * @param Codec $codec
     *
     * @return mixed
     *
     * @throws GdbotsPbjException
     * @throws DecodeValueFailed
     */
    public function decode($value, Field $field, ?Codec $codec = null);

    /**
     * Returns true if the value gets decoded and stored during runtime as a scalar value.
     *
     * @return bool
     */
    public function isScalar(): bool;

    /**
     * Returns true if the value gets encoded to a scalar value.  This is important to
     * know because a big int, date, enum, etc. is stored as an object on the message
     * but when the message is encoded to an array, json, etc. it's a scalar value.
     *
     * @return bool
     */
    public function encodesToScalar(): bool;

    /**
     * @return mixed
     */
    public function getDefault();

    public function isBoolean(): bool;

    public function isBinary(): bool;

    public function isNumeric(): bool;

    public function isString(): bool;

    public function isMessage(): bool;

    /**
     * Returns the minimum value supported by an integer type.
     *
     * @return int
     */
    public function getMin(): int;

    /**
     * Returns the maximum value supported by an integer type.
     *
     * @return int
     */
    public function getMax(): int;

    /**
     * Returns the maximum number of bytes supported by the string or binary type.
     *
     * @return int
     */
    public function getMaxBytes(): int;

    public function allowedInSet(): bool;
}
