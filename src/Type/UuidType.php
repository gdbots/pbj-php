<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\WellKnown\UuidIdentifier;

final class UuidType extends AbstractType
{
    public function guard($value, Field $field): void
    {
        $fieldName = $field->getName();
        Assertion::isInstanceOf($value, UuidIdentifier::class, null, $fieldName);
        if ($field->hasClassName()) {
            Assertion::isInstanceOf($value, $field->getClassName(), null, $fieldName);
        }
    }

    public function encode($value, Field $field, ?Codec $codec = null)
    {
        if ($value instanceof UuidIdentifier) {
            return $value->toString();
        }

        return !empty($value) ? (string)$value : null;
    }

    public function decode($value, Field $field, ?Codec $codec = null)
    {
        if (null === $value || $value instanceof UuidIdentifier) {
            return $value;
        }

        if ($codec && $codec->skipValidation() && !empty($value)) {
            return (string)$value;
        }

        /** @var UuidIdentifier $className */
        $className = $field->getClassName() ?: UuidIdentifier::class;
        if ($value instanceof $className) {
            return $value;
        }

        return $className::fromString((string)$value);
    }

    public function isScalar(): bool
    {
        return false;
    }

    public function getDefault()
    {
        return UuidIdentifier::generate();
    }

    public function isString(): bool
    {
        return true;
    }
}
