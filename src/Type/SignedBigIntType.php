<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Common\BigNumber;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Field;

final class SignedBigIntType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        /** @var BigNumber $value */
        Assertion::isInstanceOf($value, 'Gdbots\Common\BigNumber', null, $field->getName());
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
    public function encode($value, Field $field)
    {
        if ($value instanceof BigNumber) {
            return $value->getValue();
        }
        return '0';
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field)
    {
        if (null === $value || $value instanceof BigNumber) {
            return $value;
        }
        return new BigNumber((string) $value);
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
        return new BigNumber(0);
    }

    /**
     * {@inheritdoc}
     */
    public function isNumeric()
    {
        return true;
    }
}
