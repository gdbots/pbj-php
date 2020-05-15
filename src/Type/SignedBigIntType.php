<?php

namespace Gdbots\Pbj\Type;

use Brick\Math\BigInteger;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;

final class SignedBigIntType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        /** @var BigInteger $value */
        Assertion::isInstanceOf($value, BigInteger::class, null, $field->getName());
        Assertion::true(
            $value->isGreaterThanOrEqualTo('-9223372036854775808'),
            sprintf('Field [%s] cannot be less than [-9223372036854775808].', $field->getName()),
            $field->getName()
        );
        Assertion::true(
            $value->isLessThanOrEqualTo('9223372036854775807'),
            sprintf('Field [%s] cannot be greater than [9223372036854775807].', $field->getName()),
            $field->getName()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function encode($value, Field $field, Codec $codec = null)
    {
        if ($value instanceof BigInteger) {
            return (string)$value;
        }

        return '0';
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field, Codec $codec = null)
    {
        if (null === $value || $value instanceof BigInteger) {
            return $value;
        }

        return BigInteger::of((string)$value);
    }

    /**
     * {@inheritdoc}
     */
    public function isScalar()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault()
    {
        return BigInteger::zero();
    }

    /**
     * {@inheritdoc}
     */
    public function isNumeric()
    {
        return true;
    }
}
