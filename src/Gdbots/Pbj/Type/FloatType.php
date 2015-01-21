<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Field;

final class FloatType extends AbstractType
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
        return (float) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field)
    {
        return (float) $value;
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
