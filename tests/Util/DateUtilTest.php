<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj\Util;

use Gdbots\Pbj\Util\DateUtil;
use PHPUnit\Framework\TestCase;

class DateUtilTest extends TestCase
{
    public function testUtcZuluWithMicroseconds()
    {
        $expected = '2012-12-14T20:24:01.123456Z';
        $date = \DateTime::createFromFormat(DateUtil::ISO8601_ZULU, $expected);
        $actual = $date->format(DateUtil::ISO8601_ZULU);
        $this->assertSame($expected, $actual);
    }

    public function testISO8601WithMicroseconds()
    {
        $expected = '2012-12-14T20:24:01.123456+00:00';
        $date = \DateTime::createFromFormat(DateUtil::ISO8601, $expected);
        $actual = $date->format(DateUtil::ISO8601);
        $this->assertSame($expected, $actual);
    }

    public function testIsValidISO8601Date()
    {
        $this->assertTrue(DateUtil::isValidISO8601Date('2012-12-14T20:24:01.123456+00:00'));
        $this->assertTrue(DateUtil::isValidISO8601Date('2012-12-14T20:24:01+00:00'));
        $this->assertTrue(DateUtil::isValidISO8601Date('2012-12-14T20:24:01.123456Z'));
        $this->assertTrue(DateUtil::isValidISO8601Date('2012-12-14T20:24:01Z'));
        $this->assertTrue(DateUtil::isValidISO8601Date('2012-12-14T20:24:01.123456'));

        $this->assertFalse(DateUtil::isValidISO8601Date('2012-12-14T20:24:01.123456+00:00AA'));
        $this->assertFalse(DateUtil::isValidISO8601Date('2012-12-14T20:24:0100:00'));
        $this->assertFalse(DateUtil::isValidISO8601Date('cats'));
        $this->assertFalse(DateUtil::isValidISO8601Date('-1 day'));
        $this->assertFalse(DateUtil::isValidISO8601Date('1234567890'));
    }
}
