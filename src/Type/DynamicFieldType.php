<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Exception\EncodeValueFailed;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\MessageRef;

final class DynamicFieldType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        /** @var MessageRef $value */
        Assertion::isInstanceOf($value, 'Gdbots\Pbj\MessageRef', null, $field->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function encode($value, Field $field)
    {
        throw new EncodeValueFailed($value, $field, 'DynamicField must be encoded with a Serializer.');
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field)
    {
        throw new DecodeValueFailed($value, $field, 'DynamicField must be decoded with a Serializer.');
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
    public function encodesToScalar()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function allowedInSet()
    {
        return false;
    }
}
