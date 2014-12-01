<?php

namespace Gdbots\Tests\Messages\Enum;

use Gdbots\Common\AbstractEnum;

/**
 * @method static Provider AOL()
 * @method static Provider GMAIL()
 * @method static Provider HOTMAIL()
 */
final class Provider extends AbstractEnum
{
    const AOL     = 'aol';
    const GMAIL   = 'gmail';
    const HOTMAIL = 'hotmail';
}
