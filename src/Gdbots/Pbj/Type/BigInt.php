<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Common\BigNumber;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Field;

final class BigInt extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        /** @var BigNumber $value */
        Assertion::isInstanceOf($value, 'Gdbots\Common\BigNumber', null, $field->getName());
        Assertion::true(
            !$value->isNegative(),
            sprintf('Field [%s] cannot be negative.', $field->getName()),
            $field->getName()
        );
        Assertion::true(
            $value->isLessThanOrEqualTo('18446744073709551615'),
            sprintf('Field [%s] cannot be greater than [18446744073709551615].', $field->getName()),
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
        if ($value instanceof BigNumber) {
            return $value;
        }
        return new BigNumber((string) $value);
    }

    /**
     * {@inheritdoc}
     */
    public function decodesToScalar()
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
