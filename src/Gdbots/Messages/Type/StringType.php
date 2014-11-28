<?php

namespace Gdbots\Messages\Type;

use Assert\Assertion;
use Gdbots\Messages\Field;

final class StringType extends AbstractType
{
    /**
     * @see Type::guard
     */
    public function guard($value, Field $field)
    {
        Assertion::string($value, null, $field->getName());
    }

    /**
     * @see Type::encode
     */
    public function encode($value, Field $field)
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        return $value;
    }

    /**
     * @see Type::decode
     */
    public function decode($value, Field $field)
    {
        return (string) $value;
    }

    /**
     * @see Type::isString
     */
    public function isString()
    {
        return true;
    }
}