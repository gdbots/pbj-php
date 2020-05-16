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
        Assertion::isInstanceOf($value, UuidIdentifier::class, null, $field->getName());
        if ($field->hasClassName()) {
            Assertion::isInstanceOf($value, $field->getClassName(), null, $field->getName());
        }
    }

    public function encode($value, Field $field, ?Codec $codec = null)
    {
        if ($value instanceof UuidIdentifier) {
            return $value->toString();
        }

        return null;
    }

    public function decode($value, Field $field, ?Codec $codec = null)
    {
        if (empty($value)) {
            return null;
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
