<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Field;

final class UuidType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        Assertion::isInstanceOf($value, 'Gdbots\Identifiers\UuidIdentifier', null, $field->getName());
        if ($field->hasClassName()) {
            Assertion::isInstanceOf($value, $field->getClassName(), null, $field->getName());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function encode($value, Field $field)
    {
        if ($value instanceof UuidIdentifier) {
            return $value->toString();
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field)
    {
        if (empty($value)) {
            return null;
        }

        /** @var UuidIdentifier $className */
        $className = $field->getClassName() ?: 'Gdbots\Identifiers\UuidIdentifier';
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
