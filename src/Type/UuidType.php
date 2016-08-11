<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\WellKnown\UuidIdentifier;

final class UuidType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        Assertion::isInstanceOf($value, 'Gdbots\Pbj\WellKnown\UuidIdentifier', null, $field->getName());
        if ($field->hasClassName()) {
            Assertion::isInstanceOf($value, $field->getClassName(), null, $field->getName());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function encode($value, Field $field, Codec $codec = null)
    {
        if ($value instanceof UuidIdentifier) {
            return $value->toString();
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

        /** @var UuidIdentifier $className */
        $className = $field->getClassName() ?: 'Gdbots\Pbj\WellKnown\UuidIdentifier';
        if ($value instanceof $className) {
            return $value;
        }

        return $className::fromString((string) $value);
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
    public function getDefault()
    {
        return UuidIdentifier::generate();
    }

    /**
     * {@inheritdoc}
     */
    public function isString()
    {
        return true;
    }
}
