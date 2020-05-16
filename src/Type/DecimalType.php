<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;

// todo: review precision/scale handling.  this seems putrid
final class DecimalType extends AbstractType
{
    public function guard($value, Field $field): void
    {
        Assertion::float($value, null, $field->getName());
    }

    public function encode($value, Field $field, ?Codec $codec = null)
    {
        return (float)bcadd((string)$value, '0', $field->getScale());
    }

    public function decode($value, Field $field, ?Codec $codec = null)
    {
        return (float)bcadd((string)$value, '0', $field->getScale());
    }

    public function getDefault()
    {
        return 0.0;
    }

    public function isNumeric(): bool
    {
        return true;
    }

    public function getMin(): int
    {
        return -1;
    }

    public function getMax(): int
    {
        return INF;
    }
}
