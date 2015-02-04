<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Common\Util\NumberUtils;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Field;

abstract class AbstractBinaryType extends AbstractType
{
    private $decodeFromBase64 = true;
    private $encodeToBase64 = true;

    /**
     * @param bool $useBase64
     */
    public function decodeFromBase64($useBase64)
    {
        $this->decodeFromBase64 = (bool) $useBase64;
    }

    /**
     * @param bool $useBase64
     */
    public function encodeToBase64($useBase64)
    {
        $this->encodeToBase64 = (bool) $useBase64;
    }

    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        Assertion::string($value, null, $field->getName());

        // intentionally using strlen to get byte length, not mb_strlen
        $length = $this->encodeToBase64 ? strlen($this->encode($value, $field)) : strlen($value);
        $minLength = $field->getMinLength();
        $maxLength = NumberUtils::bound($field->getMaxLength(), $minLength, $this->getMaxBytes());
        $okay = $length >= $minLength && $length <= $maxLength;

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

    /**
     * {@inheritdoc}
     */
    public function encode($value, Field $field)
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        return $this->encodeToBase64 ? base64_encode($value) : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field)
    {
        $value = trim((string) $value);
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

    /**
     * {@inheritdoc}
     */
    public function isBinary()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isString()
    {
        return true;
    }
}