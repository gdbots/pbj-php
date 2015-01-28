<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Exception\EncodeValueFailed;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Message;

final class MessageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        /** @var Message $value */
        Assertion::isInstanceOf($value, 'Gdbots\Pbj\Message', null, $field->getName());
        Assertion::isInstanceOf($value, $field->getClassName(), null, $field->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function encode($value, Field $field)
    {
        throw new EncodeValueFailed($value, $field, 'Messages must be encoded with a Serializer.');
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field)
    {
        throw new DecodeValueFailed($value, $field, 'Messages must be decoded with a Serializer.');
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
}
