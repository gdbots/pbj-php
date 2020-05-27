<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Util\NumberUtil;

abstract class AbstractBinaryType extends AbstractType
{
    private bool $decodeFromBase64 = true;
    private bool $encodeToBase64 = true;

    public function decodeFromBase64(bool $useBase64): void
    {
        $this->decodeFromBase64 = $useBase64;
    }

    public function encodeToBase64(bool $useBase64): void
    {
        $this->encodeToBase64 = $useBase64;
    }

    public function guard($value, Field $field): void
    {
        Assertion::string($value, null, $field->getName());

        // intentionally using strlen to get byte length, not mb_strlen
        $length = $this->encodeToBase64 ? strlen($this->encode($value, $field)) : strlen($value);
        $minLength = $field->getMinLength();
        $maxLength = NumberUtil::bound($field->getMaxLength(), $minLength, $this->getMaxBytes());
        $okay = $length >= $minLength && $length <= $maxLength;

        if (!$okay) {
            Assertion::true(
                $okay,
                sprintf(
                    'Field [%s] must be between [%d] and [%d] bytes, [%d] bytes given.',
                    $field->getName(),
                    $minLength,
                    $maxLength,
                    $length
                ),
                $field->getName()
            );
        }
    }

    public function encode($value, Field $field, ?Codec $codec = null)
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        return $this->encodeToBase64 ? base64_encode($value) : $value;
    }

    public function decode($value, Field $field, ?Codec $codec = null)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        if (!$this->decodeFromBase64) {
            return $value;
        }

        $value = base64_decode($value, true);

        if (false === $value) {
            throw new DecodeValueFailed($value, $field, 'Strict base64_decode failed.');
        }

        return $value;
    }

    public function isBinary(): bool
    {
        return true;
    }

    public function isString(): bool
    {
        return true;
    }
}
