<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\WellKnown\GeoPoint;

final class GeoPointType extends AbstractType
{
    public function guard($value, Field $field): void
    {
        Assertion::isInstanceOf($value, GeoPoint::class, null, $field->getName());
    }

    public function encode($value, Field $field, ?Codec $codec = null)
    {
        return $codec->encodeGeoPoint($value, $field);
    }

    public function decode($value, Field $field, ?Codec $codec = null)
    {
        return $codec->decodeGeoPoint($value, $field);
    }

    public function isScalar(): bool
    {
        return false;
    }

    public function encodesToScalar(): bool
    {
        return false;
    }

    public function allowedInSet(): bool
    {
        return false;
    }
}
