<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\WellKnown\TimeUuidIdentifier;

final class TimeUuidType extends AbstractType
{
    public function guard(mixed $value, Field $field): void
    {
        $fieldName = $field->getName();
        Assertion::isInstanceOf($value, TimeUuidIdentifier::class, null, $fieldName);
        if ($field->hasClassName()) {
            Assertion::isInstanceOf($value, $field->getClassName(), null, $fieldName);
        }
    }

    public function encode(mixed $value, Field $field, ?Codec $codec = null): ?string
    {
        if ($value instanceof TimeUuidIdentifier) {
            return $value->toString();
        }

        return !empty($value) ? (string)$value : null;
    }

    public function decode(mixed $value, Field $field, ?Codec $codec = null): TimeUuidIdentifier|string|null
    {
        if (null === $value || $value instanceof TimeUuidIdentifier) {
            return $value;
        }

        if ($codec && $codec->skipValidation() && !empty($value)) {
            return (string)$value;
        }

        /** @var TimeUuidIdentifier $className */
        $className = $field->getClassName() ?: TimeUuidIdentifier::class;
        if ($value instanceof $className) {
            return $value;
        }

        return $className::fromString((string)$value);
    }

    public function isScalar(): bool
    {
        return false;
    }

    public function getDefault(): TimeUuidIdentifier
    {
        return TimeUuidIdentifier::generate();
    }

    public function isString(): bool
    {
        return true;
    }
}
