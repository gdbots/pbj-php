<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Common\Util\NumberUtils;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Field;

// todo: implement BinaryType
final class BinaryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        Assertion::string($value, null, $field->getName());

        $length = strlen($value);
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
        return base64_encode($value);
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
        return base64_decode($value);
    }

    /**
     * {@inheritdoc}
     */
    public function isString()
    {
        return true;
    }
}