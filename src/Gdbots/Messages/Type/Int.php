<?php

namespace Gdbots\Messages\Type;

use Gdbots\Messages\Assertion;
use Gdbots\Messages\Field;

final class Int extends AbstractInt
{
    /**
     * @see Type::guard
     */
    public function guard($value, Field $field)
    {
        Assertion::integer($value, null, $field->getName());
        Assertion::range($value, 0, 4294967295, null, $field->getName());
    }
}
