<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Field;

final class IntType extends AbstractIntType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        parent::guard($value, $field);
        Assertion::range($value, 0, 4294967295, null, $field->getName());
    }
}
