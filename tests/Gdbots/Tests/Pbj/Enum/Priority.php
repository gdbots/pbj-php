<?php

namespace Gdbots\Tests\Pbj\Enum;

use Gdbots\Common\Enum;

/**
 * @method static Priority NORMAL()
 * @method static Priority HIGH()
 * @method static Priority LOW()
 */
final class Priority extends Enum
{
    const NORMAL = 1;
    const HIGH   = 2;
    const LOW    = 3;
}
