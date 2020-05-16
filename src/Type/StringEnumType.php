<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Common\Enum;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Field;

final class StringEnumType extends AbstractType
{
    public function guard($value, Field $field): void
    {
        /** @var Enum $value */
        Assertion::isInstanceOf($value, Enum::class, null, $field->getName());
        Assertion::isInstanceOf($value, $field->getClassName(), null, $field->getName());
        $v = $value->getValue();
        Assertion::string($v, null, $field->getName());

        // intentionally using strlen to get byte length, not mb_strlen
        $length = strlen($v);
        $maxBytes = $this->getMaxBytes();
        $okay = $length > 0 && $length <= $maxBytes;
        Assertion::true(
            $okay,
            sprintf(
                'Field [%s] must be between [1] and [%d] bytes, [%d] bytes given.',
                $field->getName(),
                $maxBytes,
                $length
            ),
            $field->getName()
        );
    }

    public function encode($value, Field $field, ?Codec $codec = null)
    {
        if ($value instanceof Enum) {
            return (string)$value->getValue();
        }

        return null;
    }

    public function decode($value, Field $field, ?Codec $codec = null)
    {
        if (empty($value)) {
            return null;
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
