<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Field;

final class Boolean extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        Assertion::boolean($value, null, $field->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function encode($value, Field $field)
    {
        return (bool) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field)
    {
        return (bool) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault()
    {
        return false;
    }

    /**
     * @see Type::isBoolean
     */
    public function isBoolean()
    {
        return true;
    }
}