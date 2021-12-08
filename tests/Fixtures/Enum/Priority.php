<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj\Fixtures\Enum;

enum Priority: int
{
    case UNKNOWN = 0;
    case NORMAL = 1;
    case HIGH = 2;
    case LOW = 3;
}
