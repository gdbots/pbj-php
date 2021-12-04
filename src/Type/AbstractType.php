<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Enum\TypeName;
use Gdbots\Pbj\Util\StringUtil;

abstract class AbstractType implements Type
{
    /** @var self[] */
    private static array $instances = [];
    private TypeName $typeName;

    /**
     * Private constructor to ensure flyweight construction.
     *
     * @param TypeName $typeName
     */
    final private function __construct(TypeName $typeName)
    {
        $this->typeName = $typeName;
    }

    final public static function create(): self
    {
        $class = static::class;
        if (!isset(self::$instances[$class])) {
            $a = explode('\\', static::class);
            $typeName = StringUtil::toSlugFromCamel(str_replace('Type', '', end($a)));
            self::$instances[$class] = new static(TypeName::create($typeName));
        }

        return self::$instances[$class];
    }

    final public function getTypeName(): TypeName
    {
        return $this->typeName;
    }

    final public function getTypeValue(): string
    {
        return $this->typeName->getValue();
    }

    public function isScalar(): bool
    {
        return true;
    }

    public function encodesToScalar(): bool
    {
        return true;
    }

    public function getDefault()
    {
        return null;
    }

    public function isBoolean(): bool
    {
        return false;
    }

    public function isBinary(): bool
    {
        return false;
    }

    public function isNumeric(): bool
    {
        return false;
    }

    public function isString(): bool
    {
        return false;
    }

    public function isMessage(): bool
    {
        return false;
    }

    public function getMin(): int
    {
        return -2147483648;
    }

    public function getMax(): int
    {
        return 2147483647;
    }

    public function getMaxBytes(): int
    {
        return 65535;
    }

    public function allowedInSet(): bool
    {
        return true;
    }
}
