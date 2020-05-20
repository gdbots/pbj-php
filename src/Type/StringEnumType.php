<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Enum;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Field;

final class StringEnumType extends AbstractType
{
    public function guard($value, Field $field): void
    {
        $fieldName = $field->getName();

        /** @var Enum $value */
        //Assertion::isInstanceOf($value, Enum::class, null, $fieldName);
        Assertion::isInstanceOf($value, $field->getClassName(), null, $fieldName);
        $v = $value->getValue();
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

    public function encode($value, Field $field, ?Codec $codec = null)
    {
        if ($value instanceof Enum) {
            return (string)$value->getValue();
        }

        return !empty($value) ? (string)$value : null;
    }

    public function decode($value, Field $field, ?Codec $codec = null)
    {
        if (null === $value || $value instanceof Enum) {
            return $value;
        }

        if ($codec && $codec->skipValidation() && !empty($value)) {
            return (string)$value;
        }

        /** @var Enum $className */
        $className = $field->getClassName();

        try {
            return $className::create((string)$value);
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
