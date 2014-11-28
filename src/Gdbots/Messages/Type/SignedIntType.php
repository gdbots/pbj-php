<?php

namespace Gdbots\Messages\Type;

use Gdbots\Messages\Field;

final class SignedIntType extends AbstractIntType
{
    /**
     * @see Type::guard
     */
    public function guard($value, Field $field)
    {
        \Assert\that($value, null, $field->getName())
            ->integer()
            ->range(-2147483648, 2147483647);
    }
}