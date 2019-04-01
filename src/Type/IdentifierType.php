<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\WellKnown\Identifier;

final class IdentifierType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        /** @var Identifier $value */
        Assertion::isInstanceOf($value, Identifier::class, null, $field->getName());
        Assertion::isInstanceOf($value, $field->getClassName(), null, $field->getName());
        $v = $value->toString();
        //Assertion::string($v, null, $field->getName());

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
    public function encode($value, Field $field, Codec $codec = null)
    {
        if ($value instanceof Identifier) {
            return (string) $value->toString();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field, Codec $codec = null)
    {
        if (empty($value)) {
            return null;
        }

        /** @var Identifier $className */
        $className = $field->getClassName();

        try {
            return $className::fromString((string) $value);
        } catch (\Exception $e) {
            throw new DecodeValueFailed($value, $field, $e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isScalar()
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
        return 255;
    }
}
