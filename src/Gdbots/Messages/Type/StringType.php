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
        if ($field->isNullable()) {
            Assertion::nullOrString($value);
        } else {
            Assertion::string($value);
        }
    }

    /**
     * @see Type::encode
     */
    public function encode($value, Field $field)
    {
        return $value;
    }

    /**
     * @see Type::decode
     */
    public function decode($value, Field $field)
    {
        return (string) $value;
    }
}