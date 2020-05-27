<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Util;

final class URLUtil
{
    // REGEX FROM: http://immike.net/blog/2007/04/06/5-regular-expressions-every-web-programmer-should-know/
    // UPDATED ON 5-21-2010 FROM: http://data.iana.org/TLD/tlds-alpha-by-domain.txt
    const URL_MATCH = "\b
          (?:
            (?:https?):\/\/[-\w]+(?:\.\w[-\w]*)+
          |
            (?i: [a-z0-9] (?:[-a-z0-9]*[a-z0-9])? \. )+
            (?-i:
                  ac\b
                | ad\b
                | ae\b
                | aero\b
                | af\b
                | ag\b
                | ai\b
                | al\b
                | am\b
                | an\b
                | ao\b
                | aq\b
                | ar\b
                | arpa\b
                | as\b
                | asia\b
                | at\b
                | au\b
                | aw\b
                | ax\b
                | az\b
                | ba\b
                | bb\b
                | bd\b
                | be\b
                | bf\b
                | bg\b
                | bh\b
                | bi\b
                | biz\b
                | bj\b
                | bm\b
                | bn\b
                | bo\b
                | br\b
                | bs\b
                | bt\b
                | bv\b
                | bw\b
                | by\b
                | bz\b
                | ca\b
                | cat\b
                | cc\b
                | cd\b
                | cf\b
                | cg\b
                | ch\b
                | ci\b
                | ck\b
                | cl\b
                | cm\b
                | cn\b
                | co\b
                | com\b
                | coop\b
                | cr\b
                | cu\b
                | cv\b
                | cx\b
                | cy\b
                | cz\b
                | de\b
                | dj\b
                | dk\b
                | dm\b
                | do\b
                | dz\b
                | ec\b
                | edu\b
                | ee\b
                | eg\b
                | er\b
                | es\b
                | et\b
                | eu\b
                | fi\b
                | fj\b
                | fk\b
                | fm\b
                | fo\b
                | fr\b
                | ga\b
                | gb\b
                | gd\b
                | ge\b
                | gf\b
                | gg\b
                | gh\b
                | gi\b
                | gl\b
                | gm\b
                | gn\b
                | gov\b
                | gp\b
                | gq\b
                | gr\b
                | gs\b
                | gt\b
                | gu\b
                | gw\b
                | gy\b
                | hk\b
                | hm\b
                | hn\b
                | hr\b
                | ht\b
                | hu\b
                | id\b
                | ie\b
                | il\b
                | im\b
                | in\b
                | info\b
                | int\b
                | io\b
                | iq\b
                | ir\b
                | is\b
                | it\b
                | je\b
                | jm\b
                | jo\b
                | jobs\b
                | jp\b
                | ke\b
                | kg\b
                | kh\b
                | ki\b
                | km\b
                | kn\b
                | kp\b
                | kr\b
                | kw\b
                | ky\b
                | kz\b
                | la\b
                | lb\b
                | lc\b
                | li\b
                | lk\b
                | lr\b
                | ls\b
                | lt\b
                | lu\b
                | lv\b
                | ly\b
                | ma\b
                | mc\b
                | md\b
                | me\b
                | mg\b
                | mh\b
                | mil\b
                | mk\b
                | ml\b
                | mm\b
                | mn\b
                | mo\b
                | mobi\b
                | mp\b
                | mq\b
                | mr\b
                | ms\b
                | mt\b
                | mu\b
                | museum\b
                | mv\b
                | mw\b
                | mx\b
                | my\b
                | mz\b
                | na\b
                | name\b
                | nc\b
                | ne\b
                | net\b
                | nf\b
                | ng\b
                | ni\b
                | nl\b
                | no\b
                | np\b
                | nr\b
                | nu\b
                | nz\b
                | om\b
                | org\b
                | pa\b
                | pe\b
                | pf\b
                | pg\b
                | ph\b
                | pk\b
                | pl\b
                | pm\b
                | pn\b
                | pr\b
                | pro\b
                | ps\b
                | pt\b
                | pw\b
                | py\b
                | qa\b
                | re\b
                | ro\b
                | rs\b
                | ru\b
                | rw\b
                | sa\b
                | sb\b
                | sc\b
                | sd\b
                | se\b
                | sg\b
                | sh\b
                | si\b
                | sj\b
                | sk\b
                | sl\b
                | sm\b
                | sn\b
                | so\b
                | sr\b
                | st\b
                | su\b
                | sv\b
                | sy\b
                | sz\b
                | tc\b
                | td\b
                | tel\b
                | tf\b
                | tg\b
                | th\b
                | tj\b
                | tk\b
                | tl\b
                | tm\b
                | tn\b
                | to\b
                | tp\b
                | tr\b
                | travel\b
                | tt\b
                | tv\b
                | tw\b
                | tz\b
                | ua\b
                | ug\b
                | uk\b
                | us\b
                | uy\b
                | uz\b
                | va\b
                | vc\b
                | ve\b
                | vg\b
                | vi\b
                | vn\b
                | vu\b
                | wf\b
                | ws\b
                | xn\b
                | ye\b
                | yt\b
                | za\b
                | zm\b
                | zw\b
                | [a-z][a-z]\.[a-z][a-z]\b
            )
          )

          (?: : \d+ )?

          (?:
            \/
            [^?;\"<>\[\]\{\}\s\x7F-\xFF]*
            (?:
                  [\.!,?]+ [^?;\"<>\[\]\{\}\s\x7F-\xFF]+
            )*
          )?";

    /**
     * Private constructor. This class is not meant to be instantiated.
     */
    private function __construct()
    {
    }

    public static function isUrl(string $string): bool
    {
        return 1 === preg_match('/' . self::URL_MATCH . '/ix', $string);
    }

    /**
     * Outputs a "safe" url, stripped of xss, autolink on domain, and stripped of invalid chars
     *
     * @param string $url The url to process
     *
     * @return string Safe and happy URL
     */
    public static function safeUrl(string $url): string
    {
        if (empty($url)) {
            return '';
        }

        // Prepend https:// if no scheme is specified
        if (!preg_match("/^(https?):\/\//", $url))
            $url = 'https://' . $url;

        if (!self::isURL($url)) {
            return '';
        }

        return filter_var($url, FILTER_SANITIZE_URL) ?: '';
    }
}
