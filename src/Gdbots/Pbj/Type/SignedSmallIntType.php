<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Field;

final class SignedSmallIntType extends AbstractIntType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        parent::guard($value, $field);
        Assertion::range($value, -32768, 32767, null, $field->getName());
    }
}
