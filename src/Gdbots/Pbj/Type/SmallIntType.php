<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Field;

final class SmallIntType extends AbstractIntType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        parent::guard($value, $field);
        Assertion::range($value, 0, 65535, null, $field->getName());
    }
}
