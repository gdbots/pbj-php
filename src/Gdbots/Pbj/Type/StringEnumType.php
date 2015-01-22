<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Common\Enum;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Field;

final class StringEnumType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        /** @var Enum $value */
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

    /**
     * {@inheritdoc}
     */
    public function encode($value, Field $field)
    {
        if ($value instanceof Enum) {
            return (string) $value->getValue();
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field)
    {
        /** @var Enum $className */
        $className = $field->getClassName();
        if (empty($value)) {
            return $field->getDefault();
        }
        return $className::create((string) $value);
    }

    /**
     * {@inheritdoc}
     */
    public function decodesToScalar()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isString()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxBytes()
    {
        return 100;
    }
}
