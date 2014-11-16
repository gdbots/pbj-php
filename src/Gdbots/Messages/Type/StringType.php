<?php

namespace Gdbots\Messages\Type;

use Assert\Assertion;
use Gdbots\Messages\FieldDescriptor;

final class StringType extends AbstractType
{
    public function guard(FieldDescriptor $descriptor, $value)
    {
        Assertion::nullOrString($value);
    }

    public function encode(FieldDescriptor $descriptor, $value)
    {
        return $value;
    }

    public function decode(FieldDescriptor $descriptor, $value)
    {
        return (string) $value;
    }
}