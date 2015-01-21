<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Field;

final class SignedTinyIntType extends AbstractIntType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        parent::guard($value, $field);
        Assertion::range($value, -128, 127, null, $field->getName());
    }
}
