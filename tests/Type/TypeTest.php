<?php

namespace Gdbots\Tests\Pbj\Type;

use Gdbots\Common\Util\DateUtils;
use Gdbots\Pbj\Exception\AssertionFailed;
use Gdbots\Pbj\FieldBuilder;
use Gdbots\Pbj\Type\BinaryType;
use Gdbots\Pbj\Type\DateTimeType;
use Gdbots\Pbj\Type\DateType;
use Gdbots\Pbj\Type\Type;
use Gdbots\Pbj\WellKnown\GeoPoint;
use Gdbots\Tests\Pbj\Fixtures\NestedMessage;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    public function testDateType()
    {
        $utcDate = new \DateTime('2014-12-25', new \DateTimeZone('UTC'));
        $notUtcDate = new \DateTime('2014-12-25', new \DateTimeZone('America/Los_Angeles'));

        $field = FieldBuilder::create('date', DateType::create())->build();
        /** @var DateType $type */
        $type = $field->getType();

        $this->assertSame(
            $type->encode($utcDate, $field),
            $type->encode($notUtcDate, $field)
        );

        $this->assertSame(
            $type->decode($utcDate->format('Y-m-d'), $field)->format('c'),
            $type->decode($notUtcDate->format('Y-m-d'), $field)->format('c')
        );

        $this->assertSame(
            $type->decode($utcDate, $field)->format('c'),
            $type->decode($notUtcDate, $field)->format('c')
        );
    }

    public function testDateTimeType()
    {
        $expected = '2014-12-25T12:13:14.123456Z';
        $dateTime = \DateTime::createFromFormat(DateUtils::ISO8601_ZULU, $expected);
        $field = FieldBuilder::create('date_time', DateTimeType::create())->build();

        $encoded = $field->getType()->encode($dateTime, $field);
        $dateTime = \DateTime::createFromFormat(DateUtils::ISO8601_ZULU, $encoded);
        $this->assertSame($expected, $encoded);

        $decoded = $field->getType()->decode($encoded, $field);
        $this->assertSame(
            $dateTime->format(DateUtils::ISO8601_ZULU),
            $decoded->format(DateUtils::ISO8601_ZULU)
        );
    }

    public function testDateTimeTypeUtcConversion()
    {
        $notUtc = '2014-12-25T12:13:14.123456+08:00';
        $expected = '2014-12-25T04:13:14.123456Z';
        $dateTime = \DateTime::createFromFormat(DateUtils::ISO8601, $notUtc);
        $field = FieldBuilder::create('date_time', DateTimeType::create())->build();

        $encoded = $field->getType()->encode($dateTime, $field);
        $dateTime = \DateTime::createFromFormat(DateUtils::ISO8601_ZULU, $encoded);
        $this->assertSame($expected, $encoded);

        $decoded = $field->getType()->decode($encoded, $field);
        $this->assertSame(
            $dateTime->format(DateUtils::ISO8601_ZULU),
            $decoded->format(DateUtils::ISO8601_ZULU)
        );
    }

    public function testGeoPointType()
    {
        $message = NestedMessage::create();
        $geoJson = '{"type":"Point","coordinates":[102.0,0.5]}';
        $point = GeoPoint::fromArray(json_decode($geoJson, true));
        $message->set('location', $point);

        $this->assertSame($message->get('location')->getLatitude(), 0.5);
        $this->assertSame($message->get('location')->getLongitude(), 102.0);
        $this->assertSame($message->toArray()['location'], $point->toArray());
    }

    public function testBinaryType()
    {
        $type = BinaryType::create();
        $field = FieldBuilder::create('binary', $type)->build();

        $string = 'homer simpson';
        $expected = base64_encode($string);

        $encoded = $field->getType()->encode($string, $field);
        $this->assertSame($expected, $encoded);

        $decoded = $field->getType()->decode($encoded, $field);
        $this->assertSame($decoded, $string);

        /*
         * now test without the type handling the base64_encode/decode
         */
        $type->encodeToBase64(false);
        $type->decodeFromBase64(false);

        $encoded = $field->getType()->encode($expected, $field);
        $this->assertSame($expected, $encoded);

        $decoded = $field->getType()->decode($encoded, $field);
        $this->assertSame($decoded, $expected);

        $type->encodeToBase64(true);
        $type->decodeFromBase64(true);
    }

    public function testGuardMaxBytes()
    {
        foreach (['BinaryType', 'BlobType', 'MediumBlobType', 'MediumTextType', 'StringType', 'TextType'] as $typeName) {
            /** @var Type $type */
            $type = 'Gdbots\Pbj\Type\\' . $typeName;
            $field = FieldBuilder::create($typeName, $type::create())->build();
            $text = str_repeat('a', $field->getType()->getMaxBytes() + 1);
            $thrown = false;

            try {
                $field->getType()->guard($text, $field);
            } catch (AssertionFailed $e) {
                $thrown = true;
            }

            if (!$thrown) {
                $this->fail(
                    sprintf(
                        '[%s] accepted more than [%d] bytes.',
                        $typeName,
                        $field->getType()->getMaxBytes()
                    )
                );
            }
        }
    }
}
