<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\WellKnown\Microtime;

final class MicrotimeType extends AbstractType
{
    public function guard(mixed $value, Field $field): void
    {
        Assertion::isInstanceOf($value, Microtime::class, null, $field->getName());
    }

    public function encode(mixed $value, Field $field, ?Codec $codec = null): ?string
    {
        if ($value instanceof Microtime) {
            return $value->toString();
        }

        return !empty($value) ? (string)$value : null;
    }

    public function decode(mixed $value, Field $field, ?Codec $codec = null): Microtime|string|null
    {
        if (null === $value || $value instanceof Microtime) {
            return $value;
        }

        if ($codec && $codec->skipValidation() && !empty($value)) {
            return (string)$value;
        }

        return Microtime::fromString((string)$value);
    }

    public function isScalar(): bool
    {
        return false;
    }

    public function getDefault(): Microtime
    {
        return Microtime::create();
    }

    public function isNumeric(): bool
    {
        return true;
    }
}
