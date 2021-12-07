<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Field;
use StringBackedEnum;

final class StringEnumType extends AbstractType
{
    public function guard(mixed $value, Field $field): void
    {
        $fieldName = $field->getName();
        Assertion::isInstanceOf($value, $field->getClassName(), null, $fieldName);
        $v = $value->value;
        Assertion::string($v, null, $fieldName);

        // intentionally using strlen to get byte length, not mb_strlen
        $length = strlen($v);
        $maxBytes = $this->getMaxBytes();
        $okay = $length > 0 && $length <= $maxBytes;

        if (!$okay) {
            Assertion::true(
                $okay,
                sprintf(
                    'Field [%s] must be between [1] and [%d] bytes, [%d] bytes given.',
                    $fieldName,
                    $maxBytes,
                    $length
                ),
                $fieldName
            );
        }
    }

    public function encode(mixed $value, Field $field, ?Codec $codec = null): ?string
    {
        // if ($value instanceof StringBackedEnum) { // not working for some reason
        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        return !empty($value) ? (string)$value : null;
    }

    public function decode(mixed $value, Field $field, ?Codec $codec = null): \BackedEnum|string|null
    {
        // if (null === $value || $value instanceof StringBackedEnum) { // not working for some reason
        if (null === $value || $value instanceof \BackedEnum) {
            return $value;
        }

        if ($codec && $codec->skipValidation() && !empty($value)) {
            return (string)$value;
        }

        /** @var StringBackedEnum $className */
        $className = $field->getClassName();

        try {
            return $className::from((string)$value);
        } catch (\Throwable $e) {
            throw new DecodeValueFailed($value, $field, $e->getMessage());
        }
    }

    public function isScalar(): bool
    {
        return false;
    }

    public function isString(): bool
    {
        return true;
    }

    public function getMaxBytes(): int
    {
        return 100;
    }
}
