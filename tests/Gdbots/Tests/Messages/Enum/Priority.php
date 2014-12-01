<?php

namespace Gdbots\Tests\Messages\Enum;

use Gdbots\Common\AbstractEnum;

/**
 * @method static Priority NORMAL()
 * @method static Priority HIGH()
 * @method static Priority LOW()
 */
final class Priority extends AbstractEnum
{
    const NORMAL = 1;
    const HIGH   = 2;
    const LOW    = 3;
}
