<?php

namespace Gdbots\Pbj\Enum;

use Gdbots\Common\Enum;

/**
 * @method static DynamicFieldTypeName BOOL_VAL()
 * @method static DynamicFieldTypeName DATE_VAL()
 * @method static DynamicFieldTypeName FLOAT_VAL()
 * @method static DynamicFieldTypeName INT_VAL()
 * @method static DynamicFieldTypeName STRING_VAL()
 * @method static DynamicFieldTypeName TEXT_VAL()
 */
final class DynamicFieldTypeName extends Enum
{
    const BOOL_VAL = 'bool_val';
    const DATE_VAL = 'date_val';
    const FLOAT_VAL = 'float_val';
    const INT_VAL = 'int_val';
    const STRING_VAL = 'string_val';
    const TEXT_VAL = 'text_val';
}
