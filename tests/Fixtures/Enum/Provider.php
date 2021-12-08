<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj\Fixtures\Enum;

enum Provider: string
{
    case UNKNOWN = 'unknown';
    case AOL = 'aol';
    case GMAIL = 'gmail';
    case HOTMAIL = 'hotmail';
}
