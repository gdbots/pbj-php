<?php

namespace Gdbots\Tests\Messages\Enum;

use Gdbots\Common\Enum;

/**
 * @method static StringEnum UNKNOWN()
 * @method static StringEnum A_STRING()
 */
final class StringEnum extends Enum
{
    const UNKNOWN  = 'unknown';
    const A_STRING = 'string';
}
