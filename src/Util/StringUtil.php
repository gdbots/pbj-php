<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Util;

final class StringUtil
{
    // todo: memoize inflectors and maybe move inflectors to own class

    /**
     * Private constructor. This class is not meant to be instantiated.
     */
    private function __construct()
    {
    }

    public static function startsWithLetter(string $str): bool
    {
        return 1 === preg_match('/^[a-zA-Z]/', $str);
    }

    public static function toSlugFromCamel(string $camel): string
    {
        return trim(strtolower(preg_replace('/([A-Z])/', '-$1', $camel)), '-');
    }

    public static function toCamelFromSlug(string $slug): string
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $slug)));
    }

    public static function toSnakeFromCamel(string $camel): string
    {
        return trim(strtolower(preg_replace('/([A-Z])/', '_$1', $camel)), '_');
    }

    public static function toCamelFromSnake(string $snake): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $snake)));
    }

    public static function toSnakeFromSlug(string $slug): string
    {
        return str_replace('-', '_', $slug);
    }

    public static function toSlugFromSnake(string $snake): string
    {
        return str_replace('_', '-', $snake);
    }

    /**
     * Converts the input supplied to a safe xml version that can be included in
     * xml attributes and nodes without the use of CDATA.
     *
     * @param string $str
     *
     * @return string
     */
    public static function xmlEscape(string $str): string
    {
        // array used to figure what number to decrement from character order value
        // according to number of characters used to map unicode to ascii by utf-8
        $decrement = [];
        $decrement[4] = 240;
        $decrement[3] = 224;
        $decrement[2] = 192;
        $decrement[1] = 0;

        // the number of bits to shift each charNum by
        $shift = [];
        $shift[1][0] = 0;
        $shift[2][0] = 6;
        $shift[2][1] = 0;
        $shift[3][0] = 12;
        $shift[3][1] = 6;
        $shift[3][2] = 0;
        $shift[4][0] = 18;
        $shift[4][1] = 12;
        $shift[4][2] = 6;
        $shift[4][3] = 0;

        $pos = 0;
        // using standard strlen and letting loop determine # of bytes-per-char
        $len = strlen($str);
        $xml = '';

        while ($pos < $len) {
            $asciiPos = ord(substr($str, $pos, 1));
            if (($asciiPos >= 240) && ($asciiPos <= 255)) {
                // 4 chars representing one unicode character
                $thisLetter = substr($str, $pos, 4);
                $pos += 4;
            } else if (($asciiPos >= 224) && ($asciiPos <= 239)) {
                // 3 chars representing one unicode character
                $thisLetter = substr($str, $pos, 3);
                $pos += 3;
            } else if (($asciiPos >= 192) && ($asciiPos <= 223)) {
                // 2 chars representing one unicode character
                $thisLetter = substr($str, $pos, 2);
                $pos += 2;
            } else {
                // 1 char (lower ascii)
                $thisLetter = substr($str, $pos, 1);
                $pos += 1;
            }

            // process the string representing the letter to a unicode entity
            $thisLen = strlen($thisLetter);
            $thisPos = 0;
            $decimalCode = 0;
            while ($thisPos < $thisLen) {
                $thisCharOrd = ord(substr($thisLetter, $thisPos, 1));
                if ($thisPos == 0) {
                    $charNum = intval($thisCharOrd - $decrement[$thisLen]);
                    $decimalCode += ($charNum << $shift[$thisLen][$thisPos]);
                } else {
                    $charNum = intval($thisCharOrd - 128);
                    $decimalCode += ($charNum << $shift[$thisLen][$thisPos]);
                }

                $thisPos++;
            }

            if ($thisLen == 1) {
                $encodedLetter = '&#' . str_pad((string)$decimalCode, 3, '0', STR_PAD_LEFT) . ';';
            } else {
                $encodedLetter = '&#' . str_pad((string)$decimalCode, 5, '0', STR_PAD_LEFT) . ';';
            }

            $c = $decimalCode;


            if ($c > 0 && $c < 32) {
                $xml .= $encodedLetter;
            } else if ($c >= 32 && $c < 127) {
                $xml .= match ($thisLetter) {
                    '<' => '&lt;',
                    '>' => '&gt;',
                    '&' => '&amp;',
                    '"' => '&quot;',
                    default => $thisLetter,
                };
            } else {
                $xml .= $encodedLetter;
            }
        }

        // final mutant possibility (the amped amp)
        $xml = str_replace('&amp;amp;', '&amp;', $xml);
        return trim($xml);
    }

    public static function varToString($var): string
    {
        if (is_object($var)) {
            return sprintf('Object(%s)', get_class($var));
        }

        if (is_array($var)) {
            $a = [];
            foreach ($var as $k => $v) {
                $a[] = sprintf('%s => %s', $k, self::varToString($v));
            }

            return sprintf("Array(%s)", implode(', ', $a));
        }

        if (is_resource($var)) {
            return sprintf('Resource(%s)', get_resource_type($var));
        }

        if (null === $var) {
            return 'null';
        }

        if (false === $var) {
            return 'false';
        }

        if (true === $var) {
            return 'true';
        }

        return (string)$var;
    }

    public static function urlsafeB64Decode(string $input): string
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }

        return base64_decode(strtr($input, '-_', '+/'));
    }

    public static function urlsafeB64Encode(string $input): string
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }
}
