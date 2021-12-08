<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;

/**
 * @link https://en.wikipedia.org/wiki/Three-valued_logic
 * 0 = unknown
 * 1 = true
 * 2 = false
 */
final class TrinaryType extends AbstractType
{
    public function guard(mixed $value, Field $field): void
    {
        Assertion::choice($value, [0, 1, 2], null, $field->getName());
    }

    public function encode(mixed $value, Field $field, ?Codec $codec = null): int
    {
        return (int)$value;
    }

    public function decode(mixed $value, Field $field, ?Codec $codec = null): int
    {
        return (int)$value;
    }

    public function getDefault(): int
    {
        return 0;
    }

    public function isNumeric(): bool
    {
        return true;
    }

    public function getMin(): int
    {
        return 0;
    }

    public function getMax(): int
    {
        return 2;
    }

    public function allowedInSet(): bool
    {
        return false;
    }
}
