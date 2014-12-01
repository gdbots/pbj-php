<?php

namespace Gdbots\Messages\Type;

use Assert\Assertion;
use Gdbots\Messages\Field;

final class BooleanType extends AbstractType
{
    /**
     * @see Type::guard
     */
    public function guard($value, Field $field)
    {
        Assertion::boolean($value, null, $field->getName());
    }

    /**
     * @see Type::encode
     */
    public function encode($value, Field $field)
    {
        return (bool) $value;
    }

    /**
     * @see Type::decode
     */
    public function decode($value, Field $field)
    {
        return (bool) $value;
    }

    /**
     * @see Type::getDefault
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