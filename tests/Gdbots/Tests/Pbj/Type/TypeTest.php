<?php

namespace Gdbots\Tests\Pbj\Type;

use Gdbots\Common\Util\DateUtils;
use Gdbots\Pbj\FieldBuilder;
use Gdbots\Pbj\Type\DateTimeType;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    public function testDateTimeType()
    {
        $expected = '2014-12-25T12:13:14.123456+00:00';
        $dateTime = \DateTime::createFromFormat(DateUtils::ISO8601, $expected);
        $field = FieldBuilder::create('date_time', DateTimeType::create())->build();

        $encoded = $field->getType()->encode($dateTime, $field);
        $dateTime = \DateTime::createFromFormat(DateUtils::ISO8601, $encoded);
        $this->assertSame($expected, $encoded);

        $decoded = $field->getType()->decode($encoded, $field);
        $this->assertSame(
            $dateTime->format(DateUtils::ISO8601),
            $decoded->format(DateUtils::ISO8601)
        );
    }

    public function testDateTimeTypeUtcConversion()
    {
        $notUtc = '2014-12-25T12:13:14.123456+08:00';
        $expected = '2014-12-25T04:13:14.123456+00:00';
        $dateTime = \DateTime::createFromFormat(DateUtils::ISO8601, $notUtc);
        $field = FieldBuilder::create('date_time', DateTimeType::create())->build();

        $encoded = $field->getType()->encode($dateTime, $field);
        $dateTime = \DateTime::createFromFormat(DateUtils::ISO8601, $encoded);
        $this->assertSame($expected, $encoded);

        $decoded = $field->getType()->decode($encoded, $field);
        $this->assertSame(
            $dateTime->format(DateUtils::ISO8601),
            $decoded->format(DateUtils::ISO8601)
        );
    }
}
