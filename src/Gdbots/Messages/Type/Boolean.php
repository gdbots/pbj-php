<?php

namespace Gdbots\Messages\Type;

use Gdbots\Messages\Assertion;
use Gdbots\Messages\Field;

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