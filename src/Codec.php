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

    public function encodeDynamicField(DynamicField|array $dynamicField, Field $field): mixed;

    public function decodeDynamicField(mixed $value, Field $field): DynamicField|array;

    public function encodeGeoPoint(GeoPoint|array $geoPoint, Field $field): mixed;

    public function decodeGeoPoint(mixed $value, Field $field): GeoPoint|array;

    public function encodeMessage(Message $message, Field $field): mixed;

    public function decodeMessage(mixed $value, Field $field): Message;

    public function encodeMessageRef(MessageRef|array $messageRef, Field $field): mixed;

    public function decodeMessageRef(mixed $value, Field $field): MessageRef|array;
}
