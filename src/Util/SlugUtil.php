<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Util;

final class SlugUtil
{
    const VALID_SLUG_PATTERN = '/^[a-z0-9-]+$/';
    const VALID_DATED_SLUG_PATTERN = '/^([a-z0-9-]|[a-z0-9-][a-z0-9-\/]*[a-z0-9-])$/';

    /**
     * Private constructor. This class is not meant to be instantiated.
     */
    private function __construct()
    {
    }

    /**
     * Creates a slug from the text given.
     *
     * @param string $string
     * @param bool   $allowSlashes
     *
     * @return string
     */
    public static function create(string $string, bool $allowSlashes = false): string
    {
        $slug = '';
        $string = html_entity_decode($string, ENT_QUOTES);

        $string = preg_replace("/https*:\/\//", '', $string);
        for ($i = 0; $i <= strlen($string); $i++) {
            $s = substr($string, $i, 1);
            $c = '' === $s ? 0 : ord($s);
            if ($c < 128) {
                $slug .= chr($c);
            }

            if (($c >= 224 && $c <= 229) || ($c >= 192 && $c <= 198) || ($c >= 281 && $c <= 286)) {
                $slug .= 'a';
            } else if (($c >= 232 && $c <= 235) || ($c >= 200 && $c <= 203)) {
                $slug .= 'e';
            } else if (($c >= 236 && $c <= 239) || ($c >= 204 && $c <= 207)) {
                $slug .= 'i';
            } else if (($c >= 242 && $c <= 248) || ($c >= 210 && $c <= 216)) {
                $slug .= 'o';
            } else if (($c >= 249 && $c <= 252) || ($c >= 217 && $c <= 220)) {
                $slug .= 'u';
            } else if ($c == 253 || $c == 255 || $c == 221 || $c == 376) {
                $slug .= 'y';
            } else if ($c == 230 || $c == 198) {
                $slug .= 'ae';
            } else if ($c == 338 || $c == 339) {
                $slug .= 'oe';
            } else if ($c == 199 || $c == 231 || $c == 162) {
                $slug .= 'c';
            } else if ($c == 209 || $c == 241) {
                $slug .= 'n';
            } else if ($c == 352 || $c == 353) {
                $slug .= 's';
            } else if ($c == 208 || $c == 240) {
                $slug .= 'eth';
            } else if ($c == 223) {
                $slug .= 'sz';
            } else if (($c >= 8219 && $c <= 8223) || $c == 8242 || $c == 8243 || $c == 8216 || $c == 8217 || $c == 168 || $c == 180 || $c == 729 || $c == 733) {
                //all the strange curly single and double quotes
                // Ignore them
            } else if ($c == 188) {
                $slug .= '-one-quarter-';
            } else if ($c == 189) {
                $slug .= '-one-half-';
            } else if ($c == 190) {
                $slug .= '-three-quarters-';
            } else if ($c == 178) {
                $slug .= '-squared-';
            } else if ($c == 179) {
                $slug .= '-cubed-';
            } else if ($c > 127) {
                $slug .= '-';
            }
        }

        $find = [
            "'",
            "\"",
            "\\",
            "&",
            "%",
            "@",
        ];

        $repl = [
            '',
            '',
            '',
            '-and-',
            '-percent-',
            '-at-',
        ];

        $slug = str_replace($find, $repl, $slug);

        if (!$allowSlashes) {
            $slug = preg_replace("/[^a-zA-Z0-9\-]+/i", '-', $slug);
        } else {
            $slug = preg_replace("/[^a-zA-Z0-9\-\/]+/i", '-', $slug);
        }

        // replace more than one dash in a row
        $slug = preg_replace("/\-+/i", '-', $slug);

        if ($allowSlashes) {
            $slug = str_replace(['-/', '/-'], '/', $slug);
            // replace more than one slash in a row
            $slug = preg_replace("/\/+/i", '/', $slug);
        }

        // remove leading and trailing dash and slash
        $slug = trim($slug, '/-');
        $slug = strtolower($slug);

        return $slug;
    }

    /**
     * Adds the date in the format YYYY/MM/DD to the slug.
     *
     * @param string             $slug
     * @param \DateTimeInterface $date
     *
     * @return string
     */
    public static function addDate(string $slug, \DateTimeInterface $date): string
    {
        return $date->format('Y/m/d/') . self::removeDate($slug);
    }

    /**
     * Removes the date in the format YYYY/MM/DD from the slug if it is found.
     *
     * @param string $slug
     *
     * @return string
     */
    public static function removeDate(string $slug): string
    {
        $slug = trim($slug, '/');
        while (preg_match('/^\d{4}\/\d{2}\/\d{2}\/?(\S+)?/', $slug, $m)) {
            $slug = trim($m[1] ?? '', '/');
        }
        return $slug;
    }

    /**
     * Determines if the slug contains a date in the format YYYY/mm/dd
     *
     * @param string $slug
     *
     * @return bool
     */
    public static function containsDate(string $slug): bool
    {
        return 1 === preg_match('/^\d{4}\/\d{2}\/\d{2}\/?(\S+)?/', $slug);
    }

    /**
     * Translates, as best as possible, a slug back into a human readable format.
     *
     * @param string $slug
     *
     * @return string
     */
    public static function humanize(string $slug): string
    {
        $str = str_replace('-', ' ', $slug);
        $words = explode(' ', $str);
        $words = array_map('ucfirst', $words);
        return implode(' ', $words);
    }

    public static function isValid(string $string, bool $allowSlashes = false): bool
    {
        $match = $allowSlashes ? self::VALID_DATED_SLUG_PATTERN : self::VALID_SLUG_PATTERN;
        return preg_match($match, $string) > 0;
    }

    public static function createFromCamel(string $string, bool $allowSlashes = false): string
    {
        $string = trim(preg_replace('/(([A-Z]|[0-9])[^A-Z])/', ' $1', $string));
        return self::create($string, $allowSlashes);
    }
}
