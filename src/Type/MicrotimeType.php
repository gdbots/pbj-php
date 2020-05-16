<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\WellKnown\Microtime;

final class MicrotimeType extends AbstractType
{
    public function guard($value, Field $field): void
    {
        Assertion::isInstanceOf($value, Microtime::class, null, $field->getName());
    }

    public function encode($value, Field $field, ?Codec $codec = null)
    {
        if ($value instanceof Microtime) {
            return $value->toString();
        }

        return null;
    }

    public function decode($value, Field $field, ?Codec $codec = null)
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof Microtime) {
            return $value;
        }

        return Microtime::fromString((string)$value);
    }

    public function isScalar(): bool
    {
        return false;
    }

    public function getDefault()
    {
        return Microtime::create();
    }

    public function isNumeric(): bool
    {
        return true;
    }
}
