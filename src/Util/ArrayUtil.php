<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Util;

final class ArrayUtil
{
    /**
     * Private constructor. This class is not meant to be instantiated.
     */
    private function __construct()
    {
    }

    /**
     * Returns true if the array is associative
     *
     * @param array $array
     *
     * @return bool
     */
    public static function isAssoc(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}
