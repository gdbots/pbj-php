<?php
declare(strict_types=1);

namespace Gdbots\Pbj\WellKnown;

use Gdbots\Pbj\Exception\InvalidArgumentException;

/**
 * Represents a GeoJson Point value.
 * @link http://geojson.org/geojson-spec.html#point
 */
final class GeoPoint implements \JsonSerializable
{
    private float $latitude;
    private float $longitude;

    public function __construct(float $lat, float $lon)
    {
        $this->latitude = $lat;
        $this->longitude = $lon;

        if ($this->latitude > 90.0 || $this->latitude < -90.0) {
            throw new InvalidArgumentException('Latitude must be within range [-90.0, 90.0]');
        }

        if ($this->longitude > 180.0 || $this->longitude < -180.0) {
            throw new InvalidArgumentException('Longitude must be within range [-180.0, 180.0]');
        }
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public static function fromArray(array $data = []): self
    {
        if (isset($data['coordinates'])) {
            return new self($data['coordinates'][1], $data['coordinates'][0]);
        }

        throw new InvalidArgumentException('Payload must be a GeoJson "Point" type.');
    }

    public function toArray(): array
    {
        return ['type' => 'Point', 'coordinates' => [$this->longitude, $this->latitude]];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public static function fromString(string $string): self
    {
        [$lat, $long] = explode(',', $string);
        return new self($lat, $long);
    }

    public function toString(): string
    {
        return $this->latitude . ',' . $this->longitude;
    }

    public function __toString()
    {
        return $this->toString();
    }
}
