<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;

final class BooleanType extends AbstractType
{
    public function guard(mixed $value, Field $field): void
    {
        Assertion::boolean($value, null, $field->getName());
    }

    public function encode(mixed $value, Field $field, ?Codec $codec = null): bool
    {
        return (bool)$value;
    }

    public function decode(mixed $value, Field $field, ?Codec $codec = null): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function getDefault(): bool
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
