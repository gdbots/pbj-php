<?php
declare(strict_types=1);

namespace Gdbots\Pbj;

use Gdbots\Pbj\WellKnown\DynamicField;
use Gdbots\Pbj\WellKnown\GeoPoint;
use Gdbots\Pbj\WellKnown\MessageRef;

interface Codec
{
    /**
     * If validation is not enabled Codecs and Types can use
     * faster methods for encoding/decoding. This should only
     * be used with very trusted input.
     *
     * Provide a boolean to change the value.
     *
     * @param bool $skipValidation
     *
     * @return bool
     */
    public function skipValidation(?bool $skipValidation = null): bool;

    /**
     * @param DynamicField|array $dynamicField
     * @param Field              $field
     *
     * @return mixed
     */
    public function encodeDynamicField($dynamicField, Field $field);

    /**
     * @param mixed $value
     * @param Field $field
     *
     * @return DynamicField|array
     */
    public function decodeDynamicField($value, Field $field);

    /**
     * @param GeoPoint|array $geoPoint
     * @param Field          $field
     *
     * @return mixed
     */
    public function encodeGeoPoint($geoPoint, Field $field);

    /**
     * @param mixed $value
     * @param Field $field
     *
     * @return GeoPoint|array
     */
    public function decodeGeoPoint($value, Field $field);

    public function encodeMessage(Message $message, Field $field);

    public function decodeMessage($value, Field $field): Message;

    /**
     * @param MessageRef|array $messageRef
     * @param Field            $field
     *
     * @return mixed
     */
    public function encodeMessageRef($messageRef, Field $field);

    /**
     * @param mixed $value
     * @param Field $field
     *
     * @return MessageRef|array
     */
    public function decodeMessageRef($value, Field $field);
}
