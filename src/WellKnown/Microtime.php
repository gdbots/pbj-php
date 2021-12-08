<?php
declare(strict_types=1);

namespace Gdbots\Pbj\WellKnown;

use Gdbots\Pbj\Exception\InvalidArgumentException;

/**
 * Value object for microtime with methods to convert to and from integers.
 * Note that this is a unix timestamp __WITH__ microseconds but stored
 * as an integer NOT a float.
 *
 * 10 digits (unix timestamp) concatenated with 6 microsecond digits.
 *
 * @link http://php.net/manual/en/function.microtime.php
 */
final class Microtime implements \JsonSerializable
{
    /**
     * The microtime is stored as a 16 digit integer.
     */
    private int $int;
    private int $sec;
    private int $usec;

    /**
     * Private constructor to ensure static methods are used.
     */
    private function __construct()
    {
    }

    /**
     * Create a new object using the current microtime.
     *
     * @return self
     */
    public static function create(): self
    {
        return self::fromTimeOfDay(gettimeofday());
    }

    /**
     * Create a new object from a float value, typically one that is returned
     * from the microtime(true) call.
     *
     * @link http://php.net/manual/en/function.microtime.php
     *
     * @param float $float e.g. 1422060753.9581
     *
     * @return self
     */
    public static function fromFloat(float $float): self
    {
        $str = substr(str_pad(str_replace('.', '', (string)$float), 16, '0'), 0, 16);
        $m = new self();
        $m->int = (int)$str;
        $m->sec = (int)substr($str, 0, 10);
        $m->usec = (int)substr($str, -6);
        return $m;
    }

    /**
     * Create a new object from the result of a gettimeofday call that
     * is NOT returned as a float.
     *
     * @link http://php.net/manual/en/function.gettimeofday.php
     *
     * @param array $tod
     *
     * @return self
     */
    public static function fromTimeOfDay(array $tod): self
    {
        $str = $tod['sec'] . str_pad((string)$tod['usec'], 6, '0', STR_PAD_LEFT);
        $m = new self();
        $m->int = (int)$str;
        $m->sec = (int)substr($str, 0, 10);
        $m->usec = (int)substr($str, -6);
        return $m;
    }

    /**
     * Create a new object from the integer (or string) version of the microtime.
     *
     * Total digits would be unix timestamp (10) + (3-6) microtime digits.
     * Lack of precision on digits will be automatically padded with zeroes.
     *
     * @param string|int $stringOrInteger
     *
     * @return self
     * @throws InvalidArgumentException
     */
    public static function fromString(string|int $stringOrInteger): self
    {
        $int = (int)$stringOrInteger;
        $len = strlen((string)$int);
        if ($len < 13 || $len > 16) {
            throw new InvalidArgumentException(
                sprintf(
                    'Input [%d] must be between 13 and 16 digits, [%d] given.',
                    $int,
                    $len
                )
            );
        }

        if ($len < 16) {
            $int = (int)str_pad((string)$int, 16, '0');
        }

        $m = new self();
        $m->int = $int;
        $m->sec = (int)substr((string)$int, 0, 10);
        $m->usec = (int)substr((string)$int, -6);
        return $m;
    }

    /**
     * Creates a new microtime from a \DateTime object using
     * it's timestamp and microseconds.
     *
     * @param \DateTimeInterface $date
     *
     * @return self
     */
    public static function fromDateTime(\DateTimeInterface $date): self
    {
        $str = $date->format('U') . str_pad($date->format('u'), 6, '0');
        $m = new self();
        $m->int = (int)$str;
        $m->sec = (int)substr($str, 0, 10);
        $m->usec = (int)substr($str, -6);
        return $m;
    }

    public function toString(): string
    {
        return (string)$this->int;
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function getSeconds(): int
    {
        return $this->sec;
    }

    public function getMicroSeconds(): int
    {
        return $this->usec;
    }

    public function toDateTime(): \DateTimeInterface
    {
        return \DateTime::createFromFormat('U.u', $this->sec . '.' . str_pad((string)$this->usec, 6, '0', STR_PAD_LEFT));
    }

    public function toFloat(): float
    {
        return (float)($this->sec . '.' . str_pad((string)$this->usec, 6, '0', STR_PAD_LEFT));
    }
}
