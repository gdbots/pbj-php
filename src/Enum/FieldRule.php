<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Enum;

enum FieldRule: int
{
    case A_SINGLE_VALUE = 1;
    case A_SET = 2;
    case A_LIST = 3;
    case A_MAP = 4;
}
