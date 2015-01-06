<?php

namespace Gdbots\Tests\Messages\Enum;

use Gdbots\Common\Enum;

/**
 * @method static Provider AOL()
 * @method static Provider GMAIL()
 * @method static Provider HOTMAIL()
 */
final class Provider extends Enum
{
    const AOL     = 'aol';
    const GMAIL   = 'gmail';
    const HOTMAIL = 'hotmail';
}
