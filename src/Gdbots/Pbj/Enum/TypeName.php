<?php

namespace Gdbots\Pbj\Enum;

use Gdbots\Common\Enum;

/**
 * @method static TypeName BIG_INT()
 * @method static TypeName BOOLEAN()
 * @method static TypeName DATE()
 * @method static TypeName DATE_TIME()
 * @method static TypeName DECIMAL()
 * @method static TypeName FLOAT()
 * @method static TypeName INT()
 * @method static TypeName INT_ENUM()
 * @method static TypeName MEDIUM_INT()
 * @method static TypeName MICROTIME()
 * @method static TypeName MESSAGE()
 * @method static TypeName SIGNED_BIG_INT()
 * @method static TypeName SIGNED_INT()
 * @method static TypeName SIGNED_MEDIUM_INT()
 * @method static TypeName SIGNED_SMALL_INT()
 * @method static TypeName SIGNED_TINY_INT()
 * @method static TypeName SMALL_INT()
 * @method static TypeName STRING()
 * @method static TypeName STRING_ENUM()
 * @method static TypeName TINY_INT()
 */
final class TypeName extends Enum
{
    const BIG_INT = 'big-int';
    const BOOLEAN = 'boolean';
    const DATE = 'date';
    const DATE_TIME = 'date-time';
    const DECIMAL = 'decimal';
    const FLOAT = 'float';
    const INT = 'int';
    const INT_ENUM = 'int-enum';
    const MICROTIME = 'microtime';
    const MEDIUM_INT = 'medium-int';
    const MESSAGE = 'message';
    const SIGNED_BIG_INT = 'signed-big-int';
    const SIGNED_INT = 'signed-int';
    const SIGNED_MEDIUM_INT = 'signed-medium-int';
    const SIGNED_SMALL_INT = 'signed-small-int';
    const SIGNED_TINY_INT = 'signed-tiny-int';
    const SMALL_INT = 'small-int';
    const STRING = 'string';
    const STRING_ENUM = 'string-enum';
    const TINY_INT = 'tiny-int';
}
