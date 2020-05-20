<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Util\NumberUtil;

abstract class AbstractIntType extends AbstractType
{
    public function guard($value, Field $field): void
    {
        $fieldName = $field->getName();
        Assertion::integer($value, null, $fieldName);
        $intMin = $this->getMin();
        $intMax = $this->getMax();
        $min = NumberUtil::bound($field->getMin(), $intMin, $intMax);
        $max = NumberUtil::bound($field->getMax(), $intMin, $intMax);
        Assertion::range($value, $min, $max, null, $fieldName);
    }

    public function encode($value, Field $field, ?Codec $codec = null)
    {
        return (int)$value;
    }

    public function decode($value, Field $field, ?Codec $codec = null)
    {
        return (int)$value;
    }

    public function getDefault()
    {
        return 0;
    }

    public function isNumeric(): bool
    {
        return true;
    }
}
