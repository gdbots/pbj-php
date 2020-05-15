<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Enum;

use Gdbots\Common\Enum;

/**
 * @method static DynamicFieldKind BOOL_VAL()
 * @method static DynamicFieldKind DATE_VAL()
 * @method static DynamicFieldKind FLOAT_VAL()
 * @method static DynamicFieldKind INT_VAL()
 * @method static DynamicFieldKind STRING_VAL()
 * @method static DynamicFieldKind TEXT_VAL()
 */
final class DynamicFieldKind extends Enum
{
    const BOOL_VAL = 'bool_val';
    const DATE_VAL = 'date_val';
    const FLOAT_VAL = 'float_val';
    const INT_VAL = 'int_val';
    const STRING_VAL = 'string_val';
    const TEXT_VAL = 'text_val';
}
