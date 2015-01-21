<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Field;

final class SignedIntType extends AbstractIntType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        parent::guard($value, $field);
        Assertion::range($value, -2147483648, 2147483647, null, $field->getName());
    }
}
