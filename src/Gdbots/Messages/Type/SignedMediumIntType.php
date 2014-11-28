<?php

namespace Gdbots\Messages\Type;

use Gdbots\Messages\Field;

final class SignedMediumIntType extends AbstractIntType
{
    /**
     * @see Type::guard
     */
    public function guard($value, Field $field)
    {
        \Assert\that($value, null, $field->getName())
            ->integer()
            ->range(-8388608, 8388607);
    }
}