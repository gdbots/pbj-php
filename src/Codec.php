<?php
declare(strict_types=1);

namespace Gdbots\Pbj;

use Gdbots\Pbj\WellKnown\DynamicField;
use Gdbots\Pbj\WellKnown\GeoPoint;
use Gdbots\Pbj\WellKnown\MessageRef;

interface Codec
{
    public function encodeDynamicField(DynamicField $dynamicField, Field $field);

    public function decodeDynamicField($value, Field $field): DynamicField;

    public function encodeGeoPoint(GeoPoint $geoPoint, Field $field);

    public function decodeGeoPoint($value, Field $field): GeoPoint;

    public function encodeMessage(Message $message, Field $field);

    public function decodeMessage($value, Field $field): Message;

    public function encodeMessageRef(MessageRef $messageRef, Field $field);

    public function decodeMessageRef($value, Field $field): MessageRef;
}
