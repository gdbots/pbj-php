<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj\WellKnown;

use Gdbots\Pbj\Exception\InvalidArgumentException;
use Gdbots\Pbj\WellKnown\GeoPoint;
use PHPUnit\Framework\TestCase;

class GeoPointTest extends TestCase
{
    public function testCreate(): void
    {
        $geoPoint = new GeoPoint(45.5, -90.5);
        $this->assertSame(45.5, $geoPoint->getLatitude());
        $this->assertSame(-90.5, $geoPoint->getLongitude());

        $expected = [
            'type'        => 'Point',
            'coordinates' => [-90.5, 45.5],
        ];
        $this->assertSame($geoPoint->toArray(), $expected);
        $this->assertSame('45.5,-90.5', $geoPoint->toString());
    }

    public function testFromArray(): void
    {
        $geoPoint = GeoPoint::fromArray(['coordinates' => [90.5, -45.5]]);
        $this->assertSame(-45.5, $geoPoint->getLatitude());
        $this->assertSame(90.5, $geoPoint->getLongitude());

        $this->expectException(InvalidArgumentException::class);
        GeoPoint::fromArray([]);
    }

    public function testFromString(): void
    {
        $geoPoint = GeoPoint::fromString('45.5,-90.5');
        $this->assertSame(45.5, $geoPoint->getLatitude());
        $this->assertSame(-90.5, $geoPoint->getLongitude());
    }
}
