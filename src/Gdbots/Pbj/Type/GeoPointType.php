<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Common\GeoPoint;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Exception\EncodeValueFailed;
use Gdbots\Pbj\Field;

final class GeoPointType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        /** @var GeoPoint $value */
        Assertion::isInstanceOf($value, 'Gdbots\Common\GeoPoint', null, $field->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function encode($value, Field $field)
    {
        throw new EncodeValueFailed($value, $field, 'GeoPoints must be encoded with a Serializer.');
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field)
    {
        throw new DecodeValueFailed($value, $field, 'GeoPoints must be decoded with a Serializer.');
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
