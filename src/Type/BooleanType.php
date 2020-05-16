<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;

final class BooleanType extends AbstractType
{
    public function guard($value, Field $field): void
    {
        Assertion::boolean($value, null, $field->getName());
    }

    public function encode($value, Field $field, ?Codec $codec = null)
    {
        return (bool)$value;
    }

    public function decode($value, Field $field, ?Codec $codec = null)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function getDefault()
    {
        return false;
    }

    public function isBoolean(): bool
    {
        return true;
    }

    public function allowedInSet(): bool
    {
        return false;
    }
}
