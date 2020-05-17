<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Util;

final class NumberUtil
{
    /**
     * Private constructor. This class is not meant to be instantiated.
     */
    private function __construct()
    {
    }

    /**
     * Returns an integer within a boundary.
     *
     * @param int $number
     * @param int $min
     * @param int $max
     *
     * @return int
     */
    public static function bound(int $number, int $min = 0, int $max = PHP_INT_MAX): int
    {
        return min(max($number, $min), $max);
    }
}
