<?php

namespace Gdbots\Pbj;

use Gdbots\Pbj\WellKnown\DynamicField;
use Gdbots\Pbj\WellKnown\GeoPoint;

interface Codec
{
    /**
     * @param Message $message
     * @param Field $field
     *
     * @return mixed
     */
    public function encodeMessage(Message $message, Field $field);

    /**
     * @param mixed $value
     * @param Field $field
     *
     * @return Message
     */
    public function decodeMessage($value, Field $field);

    /**
     * @param MessageRef $messageRef
     * @param Field $field
     *
     * @return mixed
     */
    public function encodeMessageRef(MessageRef $messageRef, Field $field);

    /**
     * @param mixed $value
     * @param Field $field
     *
     * @return MessageRef
     */
    public function decodeMessageRef($value, Field $field);

    /**
     * @param GeoPoint $geoPoint
     * @param Field $field
     *
     * @return mixed
     */
    public function encodeGeoPoint(GeoPoint $geoPoint, Field $field);

    /**
     * @param mixed $value
     * @param Field $field
     *
     * @return GeoPoint
     */
    public function decodeGeoPoint($value, Field $field);

    /**
     * @param DynamicField $dynamicField
     * @param Field $field
     *
     * @return mixed
     */
    public function encodeDynamicField(DynamicField $dynamicField, Field $field);

    /**
     * @param mixed $value
     * @param Field $field
     *
     * @return DynamicField
     */
    public function decodeDynamicField($value, Field $field);
}
