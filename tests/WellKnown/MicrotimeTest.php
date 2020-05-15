<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj\WellKnown;

use Gdbots\Pbj\WellKnown\Microtime;
use PHPUnit\Framework\TestCase;

/**
 * Some of the tests run many iterations to ensure that different values
 * returned from microtime() and gettimeofday() calls cover more possibilites.
 */
class MicrotimeTest extends TestCase
{
    protected int $testCount = 2500;

    public function testFromTimeOfDay()
    {
        $i = $this->testCount;
        do {
            $tod = gettimeofday();
            $sec = $tod['sec'];
            $usec = $tod['usec'];
            $str = $sec . str_pad((string)$tod['usec'], 6, '0', STR_PAD_LEFT);
            $m = Microtime::fromTimeOfDay($tod);

            $this->assertSame($sec, $m->getSeconds());
            $this->assertSame($sec, (int)$m->toDateTime()->format('U'));
            $this->assertSame($usec, $m->getMicroSeconds());
            $this->assertSame($str, $m->toString());
            --$i;
        } while ($i > 0);
    }

    public function testFromString()
    {
        $i = $this->testCount;
        do {
            $tod = gettimeofday();
            $sec = $tod['sec'];
            $usec = $tod['usec'];
            $str = $sec . str_pad((string)$tod['usec'], 6, '0', STR_PAD_LEFT);
            $m = Microtime::fromString($str);

            $this->assertSame($sec, $m->getSeconds());
            $this->assertSame($sec, (int)$m->toDateTime()->format('U'));
            $this->assertSame($usec, $m->getMicroSeconds());
            $this->assertSame($str, $m->toString());
            --$i;
        } while ($i > 0);
    }

    /**
     * verifies that the microsecond precision is properly padded with
     * zeroes when a full 6 digits are not provided.  padding is done on
     * the right side.  e.g. 123 becomes 123000,
     */
    public function testFromStringPrecision()
    {
        $sec = time();
        $i = 6;
        do {
            $usec = str_repeat('1', $i);
            $usecFixed = (int)str_pad((string)$usec, 6, '0');
            $str = $sec . $usecFixed;
            $m = Microtime::fromString($sec . $usec);
            $this->assertSame($sec, $m->getSeconds());
            $this->assertSame($sec, (int)$m->toDateTime()->format('U'));
            $this->assertSame($usecFixed, $m->getMicroSeconds());
            $this->assertSame($str, $m->toString());
            --$i;
        } while ($i > 2);
    }

    public function testToDateTime()
    {
        $microtime = microtime(true);
        [$sec, $usec] = explode('.', (string)$microtime);
        $usec = str_pad((string)$usec, 6, '0');
        $date = \DateTime::createFromFormat('U.u', $sec . '.' . $usec);
        $m = Microtime::fromString($sec . $usec);
        $this->assertSame($date->format('Y-m-d H:i:s.u'), $m->toDateTime()->format('Y-m-d H:i:s.u'));
        $this->assertSame(
            Microtime::fromDateTime($date)->toDateTime()->format('Y-m-d H:i:s.u'),
            $m->toDateTime()->format('Y-m-d H:i:s.u')
        );
    }

    public function testDateTimeComparison()
    {
        $microtime = microtime(true);
        [$sec, $usec] = explode('.', (string)$microtime);
        $usec = str_pad((string)$usec, 6, '0');
        $date = \DateTime::createFromFormat('U.u', $sec . '.' . $usec);
        $m = Microtime::fromString($sec . $usec);
        $this->assertSame($date->format('Y-m-d H:i:s.u'), $m->toDateTime()->format('Y-m-d H:i:s.u'));
        $this->assertEquals($date, $m->toDateTime());
        $this->assertEquals($m->toDateTime()->getOffset(), (new \DateTime('UTC'))->getOffset());
    }

    /**
     * Funky test as float values get rounded when you clip digits and recreate
     * them so what we're doing is verifying the float that is regenerated
     * from the object results in the same 16 digit integer.
     */
    public function testToFloat()
    {
        $i = $this->testCount;
        do {
            $microtime = microtime(true);
            $m = Microtime::fromFloat($microtime);
            $f1 = substr(str_pad(str_replace('.', '', $microtime), 16, '0'), 0, 16);
            $f2 = substr(str_pad(str_replace('.', '', $m->toFloat()), 16, '0'), 0, 16);
            $this->assertSame($f1, $f2);
            --$i;
        } while ($i > 0);
    }
}
