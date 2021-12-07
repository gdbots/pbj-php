<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Field;
use IntBackedEnum;

final class IntEnumType extends AbstractType
{
    public function guard(mixed $value, Field $field): void
    {
        $fieldName = $field->getName();
        Assertion::isInstanceOf($value, $field->getClassName(), null, $fieldName);
        Assertion::integer($value->value, null, $field->getName());
        Assertion::range($value->value, $this->getMin(), $this->getMax(), null, $fieldName);
    }

    public function encode(mixed $value, Field $field, ?Codec $codec = null): int
    {
        // if ($value instanceof IntBackedEnum) { // not working for some reason
        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        return strlen((string)$value) ? (int)$value : 0;
    }

    public function decode(mixed $value, Field $field, ?Codec $codec = null): \BackedEnum|int|null
    {
        // if (null === $value || $value instanceof IntBackedEnum) {
        if (null === $value || $value instanceof \BackedEnum) {
            return $value;
        }

        if ($codec && $codec->skipValidation() && strlen((string)$value)) {
            return (int)$value;
        }

        /** @var IntBackedEnum $className */
        $className = $field->getClassName();

        try {
            return $className::from((int)$value);
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
