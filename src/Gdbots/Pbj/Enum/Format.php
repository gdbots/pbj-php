<?php

namespace Gdbots\Pbj\Enum;

use Gdbots\Common\Enum;

/**
 * @link http://spacetelescope.github.io/understanding-json-schema/reference/string.html#format
 *
 * @method static Format UNKNOWN()
 * @method static Format DATE()
 * @method static Format DATE_TIME()
 * @method static Format EMAIL()
 */
final class Format extends Enum
{
    const UNKNOWN = 'unknown';
    const DATE = 'date';
    const DATE_TIME = 'date-time';
    const EMAIL = 'email';
}
