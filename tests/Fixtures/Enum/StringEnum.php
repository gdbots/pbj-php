<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj\Fixtures\Enum;

enum StringEnum: string
{
    case UNKNOWN = 'unknown';
    case A_STRING = 'string';
}
