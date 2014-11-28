<?php

namespace Gdbots\Messages\Type;

use Gdbots\Messages\Field;

final class SignedSmallIntType extends AbstractIntType
{
    /**
     * @see Type::guard
     */
    public function guard($value, Field $field)
    {
        \Assert\that($value, null, $field->getName())
            ->integer()
            ->range(-32768, 32767);
    }
}