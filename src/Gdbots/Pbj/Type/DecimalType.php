<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Field;

// todo: review precision/scale handling.  this seems putrid
final class DecimalType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        Assertion::float($value, null, $field->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function encode($value, Field $field)
    {
        return (float) bcadd((float) $value, '0', $field->getScale());
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field)
    {
        return (float) bcadd((float) $value, '0', $field->getScale());
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault()
    {
        return 0.0;
    }

    /**
     * {@inheritdoc}
     */
    public function isNumeric()
    {
        return true;
    }
}
