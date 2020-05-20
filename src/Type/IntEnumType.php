<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Enum;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Field;

final class IntEnumType extends AbstractType
{
    public function guard($value, Field $field): void
    {
        $fieldName = $field->getName();

        /** @var Enum $value */
        // Assertion::isInstanceOf($value, Enum::class, null, $fieldName);
        Assertion::isInstanceOf($value, $field->getClassName(), null, $fieldName);
        Assertion::integer($value->getValue(), null, $field->getName());
        Assertion::range($value->getValue(), $this->getMin(), $this->getMax(), null, $fieldName);
    }

    public function encode($value, Field $field, ?Codec $codec = null)
    {
        if ($value instanceof Enum) {
            return (int)$value->getValue();
        }

        return strlen((string)$value) ? (int)$value : 0;
    }

    public function decode($value, Field $field, ?Codec $codec = null)
    {
        if (null === $value || $value instanceof Enum) {
            return $value;
        }

        if ($codec && $codec->skipValidation() && strlen((string)$value)) {
            return (int)$value;
        }

        /** @var Enum $className */
        $className = $field->getClassName();

        try {
            return $className::create((int)$value);
        } catch (\Throwable $e) {
            throw new DecodeValueFailed($value, $field, $e->getMessage());
        }
    }

    public function isScalar(): bool
    {
        return false;
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
        return 65535;
    }
}
