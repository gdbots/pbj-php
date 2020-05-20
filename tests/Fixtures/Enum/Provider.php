<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj\Fixtures\Enum;

use Gdbots\Pbj\Enum;

/**
 * @method static Provider UNKNOWN()
 * @method static Provider AOL()
 * @method static Provider GMAIL()
 * @method static Provider HOTMAIL()
 */
final class Provider extends Enum
{
    const UNKNOWN = 'unknown';
    const AOL = 'aol';
    const GMAIL = 'gmail';
    const HOTMAIL = 'hotmail';
}
