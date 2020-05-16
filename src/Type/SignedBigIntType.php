<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Brick\Math\BigInteger;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;

final class SignedBigIntType extends AbstractType
{
    public function guard($value, Field $field): void
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

    public function encode($value, Field $field, ?Codec $codec = null)
    {
        if ($value instanceof BigInteger) {
            return (string)$value;
        }

        return '0';
    }

    public function decode($value, Field $field, ?Codec $codec = null)
    {
        if (null === $value || $value instanceof BigInteger) {
            return $value;
        }

        return BigInteger::of((string)$value);
    }

    public function isScalar(): bool
    {
        return false;
    }

    public function getDefault()
    {
        return BigInteger::zero();
    }

    public function isNumeric(): bool
    {
        return true;
    }
}
