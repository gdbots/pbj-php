<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Util;

final class DateUtil
{
    /**
     * @link  https://en.wikipedia.org/wiki/ISO_8601
     * @const string
     */
    const ISO8601_ZULU = 'Y-m-d\TH:i:s.u\Z';

    /**
     * This format differs from php's builtin @see \DateTime::ISO8601
     * in that is uses "P" instead of "O" to ensure a colon in the
     * gmt offset.
     *
     * @const string
     */
    const ISO8601 = 'Y-m-d\TH:i:s.uP';

    /**
     * October 15, 1582 UTC
     * @const int
     */
    const MIN_UTC_TIME = -12219292800;

    /**
     * Private constructor. This class is not meant to be instantiated.
     */
    private function __construct()
    {
    }

    /**
     * Returns true if it's a valid timestamp.
     *
     * @param string|int $timestamp
     * @param bool       $allowNegative
     *
     * @return bool
     */
    public static function isValidTimestamp(string|int $timestamp, bool $allowNegative = false): bool
    {
        $timestamp = (string)$timestamp;

        if ($allowNegative) {
            return ((string)(int)$timestamp === $timestamp)
                && ($timestamp <= PHP_INT_MAX)
                && ($timestamp >= self::MIN_UTC_TIME);
        }

        return ((string)(int)$timestamp === $timestamp)
            && ($timestamp <= PHP_INT_MAX)
            && ($timestamp >= 0);
    }

    /**
     * Returns true if the provided string is a valid ISO8601 formatted date-time.
     *
     * Taken from facebook sdk...
     *
     * @link https://developers.facebook.com/docs/graph-api/using-graph-api/#readmodifiers
     * @link http://www.cl.cam.ac.uk/~mgk25/iso-time.html
     * @link http://en.wikipedia.org/wiki/ISO_8601
     *
     * @param string $string
     *
     * @return bool
     */
    public static function isValidISO8601Date(string $string): bool
    {
        // This insane regex was yoinked from here:
        // http://www.pelagodesign.com/blog/2009/05/20/iso-8601-date-validation-that-doesnt-suck/
        // ...and I'm all like:
        // http://thecodinglove.com/post/95378251969/when-code-works-and-i-dont-know-why
        $crazyInsaneRegexThatSomehowDetectsIso8601 = '/^([\+-]?\d{4}(?!\d{2}\b))'
            . '((-?)((0[1-9]|1[0-2])(\3([12]\d|0[1-9]|3[01]))?'
            . '|W([0-4]\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\d'
            . '|[12]\d{2}|3([0-5]\d|6[1-6])))([T\s]((([01]\d|2[0-3])'
            . '((:?)[0-5]\d)?|24\:?00)([\.,]\d+(?!:))?)?(\17[0-5]\d'
            . '([\.,]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)?$/';
        return preg_match($crazyInsaneRegexThatSomehowDetectsIso8601, $string) === 1;
    }
}
