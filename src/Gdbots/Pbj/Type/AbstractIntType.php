<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Common\Util\NumberUtils;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Field;

abstract class AbstractIntType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        Assertion::integer($value, null, $field->getName());

        $min = $field->getMin();
        $max = $field->getMax();

        if ($min === 0 && $max === 0) {
            return;
        }

        $max = $max ?: 2147483647;
        $max = NumberUtils::bound($max, -2147483648, 2147483647);
        $min = NumberUtils::bound($min, -2147483648, $max);
        Assertion::range($value, $min, $max, null, $field->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function encode($value, Field $field)
    {
        return (int) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field)
    {
        return (int) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isNumeric()
    {
        return true;
    }
}