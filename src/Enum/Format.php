<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Enum;

/**
 * @link http://spacetelescope.github.io/understanding-json-schema/reference/string.html#format
 */
enum Format: string
{
    case UNKNOWN = 'unknown';
    case DATE = 'date';
    case DATE_TIME = 'date-time';
    case EMAIL = 'email';
    case HASHTAG = 'hashtag';
    case HOSTNAME = 'hostname';
    case IPV4 = 'ipv4';
    case IPV6 = 'ipv6';
    case SLUG = 'slug';
    case URI = 'uri';
    case URL = 'url';
    case UUID = 'uuid';
}
