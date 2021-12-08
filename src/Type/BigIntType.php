<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Brick\Math\BigInteger;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;

final class BigIntType extends AbstractType
{
    public function guard(mixed $value, Field $field): void
    {
        $fieldName = $field->getName();
        /** @var BigInteger $value */
        Assertion::isInstanceOf($value, BigInteger::class, null, $fieldName);
        Assertion::true(
            !$value->isNegative(),
            'Field cannot be negative.',
            $fieldName
        );
        Assertion::true(
            $value->isLessThanOrEqualTo('18446744073709551615'),
            'Field cannot be greater than [18446744073709551615].',
            $fieldName
        );
    }

    public function encode(mixed $value, Field $field, ?Codec $codec = null): string
    {
        if ($value instanceof BigInteger) {
            return (string)$value;
        }

        $str = (string)$value;
        return strlen($str) ? $str : '0';
    }

    public function decode(mixed $value, Field $field, ?Codec $codec = null): BigInteger|string|null
    {
        if (null === $value || $value instanceof BigInteger) {
            return $value;
        }

        if ($codec && $codec->skipValidation() && strlen((string)$value)) {
            return (string)$value;
        }

        return BigInteger::of((string)$value);
    }

    public function isScalar(): bool
    {
        return false;
    }

    public function getDefault(): BigInteger
    {
        return BigInteger::zero();
    }

    public function isNumeric(): bool
    {
        return true;
    }
}
