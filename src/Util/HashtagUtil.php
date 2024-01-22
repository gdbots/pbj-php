<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Util;

/**
 * HashtagUtil has methods for converting text to a hashtag
 * and finding/validating hash tags.
 *
 * Current version does NOT support international hashtags.
 *
 * #hashtags how do they work?  magnets?
 *
 * #############
 * #### Rule below doesn't seem to be a rule anymore.  twitter does allow for hashtags
 * #### with leading numbers.  #2cellos or #50cent or #2014lol
 * #### - result must start with a letter (leading numbers are automatically removed) #####
 * #############
 *
 * - result must have at least one letter
 * - result cannot start with an underscore (leading _ automatically removed)
 * - all special chars and accent chars removed
 *      Beyoncé Knowles becomes BeyonceKnowles (makes it url friendly)
 * - result cannot be greater than 139 characters
 *
 * @see http://twitter.pbworks.com/w/page/1779812/Hashtags
 *
 */
final class HashtagUtil
{
    /**
     * Private constructor. This class is not meant to be instantiated.
     */
    private function __construct()
    {
    }

    /**
     * Converts special chars to more url friendly versions.
     *
     * @param string $str
     *
     * @return string
     */
    public static function normalize(string $str): string
    {
        $str = strtr($str, ['ő' => 'o', 'ű' => 'u', 'Ő' => 'O', 'Ű' => 'U']);
        $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖŐØÙÚÛÜŰÝÞßàáâãäåæçèéêëìíîïðñòóôõöőøùúûűüýýþÿŔŕ';
        $b = 'AAAAAAACEEEEIIIIDNOOOOOOOUUUUUYbsaaaaaaaceeeeiiiidnooooooouuuuuyybyRr';
        $str = self::utf8_to_iso8859_1($str);
        $str = str_replace('?', '', $str);
        $str = strtr($str, self::utf8_to_iso8859_1($a), $b);
        return self::iso8859_1_to_utf8($str);
    }

    /**
     * Converts a string into a hash tag.  Hashtag result may be null if it
     * cannot be converted.
     *
     * @param string $str
     * @param bool   $camelize
     *
     * @return string|null
     */
    public static function create(string $str, bool $camelize = true): ?string
    {
        // remove special chars (accents, etc.)
        $str = trim(self::normalize($str));
        $str = ltrim($str, '#_ ');

        // handle some punctuation and convertable chars
        $find = ["'", "?", "#", "/", "\"", "\\", "&amp;", "&", "%", "@"];
        $repl = ['', '', '', '', '', '', ' And ', ' And ', ' Percent ', ' At '];
        $str = str_replace($find, $repl, $str);

        // replace everything else and split up the words
        $str = preg_replace('/[^a-zA-Z0-9_]/', ':', $str);
        if ($camelize) {
            $str = strtolower(preg_replace('/([A-Z])/', ':$1', $str));
            $str = str_replace(' ', '', ucwords(str_replace(':', ' ', $str)));
        } else {
            $str = str_replace(' ', '', str_replace(':', ' ', $str));
        }

        $str = ltrim($str, '_');
        $hashtag = '';
        $foundLetter = false;
        $len = strlen($str);
        if ($len > 139) {
            return null;
        }

        for ($i = 0; $i < $len; $i++) {
            $char = $str[$i];
            $hashtag .= $char;

            if (!$foundLetter && !is_numeric($char)) {
                $foundLetter = true;
            }
        }

        if (!$foundLetter) {
            return null;
        }

        return empty($hashtag) ? null : $hashtag;
    }

    /**
     * Extracts all of the valid hashtags from a string.  multi-line strings
     * will work with this method.
     *
     * @param string $str
     *
     * @return array
     */
    public static function extract(string $str): array
    {
        preg_match_all("/(^|[\n ])#([a-z0-9_-]*)/is", $str, $matches);

        if (!is_array($matches) || !count($matches)) {
            return [];
        }

        $hashtags = [];
        foreach ($matches[0] as $match) {
            $match = ltrim(trim($match), '#_ ');
            if (self::isValid($match)) {
                $hashtags[strtolower($match)] = $match;
            }
        }

        return array_values($hashtags);
    }

    /**
     * Returns true if the provided hashtag conforms to the rules.
     *
     * @param string $hashtag
     *
     * @return bool
     */
    public static function isValid(string $hashtag): bool
    {
        $hashtag = ltrim($hashtag, '#');
        if (empty($hashtag)) {
            return false;
        }

        $test = preg_replace('/[^a-zA-Z0-9_]/', '', $hashtag);
        if ($test !== $hashtag) {
            return false;
        }

        if ('_' === $hashtag[0]) {
            return false;
        }

        $len = strlen($hashtag);
        if ($len > 139) {
            return false;
        }

        for ($i = 0; $i < $len; $i++) {
            if (!is_numeric($hashtag[$i])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Converts a hashtag into a more human readable version.
     * This isn't perfect as #MCHammer would become "M C Hammer".
     * It's good nuff.
     *
     * @param string $hashtag
     *
     * @return string
     */
    public static function humanize(string $hashtag): string
    {
        $hashtag = ltrim($hashtag, '#');
        $hashtag = strtolower(preg_replace('/([A-Z])/', ':$1', $hashtag));
        return ucwords(str_replace(':', ' ', $hashtag));
    }

    private static function iso8859_1_to_utf8(string $s): string
    {
        $s .= $s;
        $len = \strlen($s);

        for ($i = $len >> 1, $j = 0; $i < $len; ++$i, ++$j) {
            switch (true) {
                case $s[$i] < "\x80":
                    $s[$j] = $s[$i];
                    break;
                case $s[$i] < "\xC0":
                    $s[$j] = "\xC2";
                    $s[++$j] = $s[$i];
                    break;
                default:
                    $s[$j] = "\xC3";
                    $s[++$j] = \chr(\ord($s[$i]) - 64);
                    break;
            }
        }

        return substr($s, 0, $j);
    }

    private static function utf8_to_iso8859_1(string $s): string
    {
        $len = \strlen($s);

        for ($i = 0, $j = 0; $i < $len; ++$i, ++$j) {
            switch ($s[$i] & "\xF0") {
                case "\xC0":
                case "\xD0":
                    $c = (\ord($s[$i] & "\x1F") << 6) | \ord($s[++$i] & "\x3F");
                    $s[$j] = $c < 256 ? \chr($c) : '?';
                    break;

                case "\xF0":
                    ++$i;
                // no break

                case "\xE0":
                    $s[$j] = '?';
                    $i += 2;
                    break;

                default:
                    $s[$j] = $s[$i];
            }
        }

        return substr($s, 0, $j);
    }
}
