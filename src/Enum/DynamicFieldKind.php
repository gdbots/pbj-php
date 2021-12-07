<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Enum;

enum DynamicFieldKind: string
{
    case BOOL_VAL = 'bool_val';
    case DATE_VAL = 'date_val';
    case FLOAT_VAL = 'float_val';
    case INT_VAL = 'int_val';
    case STRING_VAL = 'string_val';
    case TEXT_VAL = 'text_val';
}
