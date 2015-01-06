<?php

namespace Gdbots\Messages\Type;

use Assert\Assertion;
use Gdbots\Messages\Field;
use Moontoast\Math\BigNumber;

final class SignedBigIntType extends AbstractType
{
    /**
     * @see Type::guard
     */
    public function guard($value, Field $field)
    {
        /** @var BigNumber $value */
        Assertion::isInstanceOf($value, 'Moontoast\Math\BigNumber', null, $field->getName());
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
     * @see Type::encode
     */
    public function encode($value, Field $field)
    {
        if ($value instanceof BigNumber) {
            return $value->getValue();
        }
        return '0';
    }

    /**
     * @see Type::decode
     */
    public function decode($value, Field $field)
    {
        if ($value instanceof BigNumber) {
            return $value;
        }
        return new BigNumber((string) $value);
    }

    /**
     * @see Type::getDefault
     */
    public function getDefault()
    {
        return new BigNumber(0);
    }

    /**
     * @see Type::isNumeric
     */
    public function isNumeric()
    {
        return true;
    }
}
