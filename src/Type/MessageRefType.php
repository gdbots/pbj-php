<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\WellKnown\MessageRef;

final class MessageRefType extends AbstractType
{
    public function guard(mixed $value, Field $field): void
    {
        Assertion::isInstanceOf($value, MessageRef::class, null, $field->getName());
    }

    public function encode(mixed $value, Field $field, ?Codec $codec = null): ?array
    {
        if (null === $value) {
            return null;
        }

        return $codec->encodeMessageRef($value, $field);
    }

    public function decode(mixed $value, Field $field, ?Codec $codec = null): MessageRef|array|null
    {
        if (null === $value || $value instanceof MessageRef) {
            return $value;
        }

        return $codec->decodeMessageRef($value, $field);
    }

    public function isScalar(): bool
    {
        return false;
    }

    public function encodesToScalar(): bool
    {
        return false;
    }

    public function allowedInSet(): bool
    {
        return false;
    }
}
