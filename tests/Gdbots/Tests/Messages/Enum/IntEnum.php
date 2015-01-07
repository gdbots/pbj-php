<?php

namespace Gdbots\Tests\Messages\Enum;

use Gdbots\Common\Enum;

/**
 * @method static IntEnum UNKNOWN()
 * @method static IntEnum A_INT()
 */
final class IntEnum extends Enum
{
    const UNKNOWN = 0;
    const A_INT   = 1;
}
