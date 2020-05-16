<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Common\Util\NumberUtils;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;

abstract class AbstractIntType extends AbstractType
{
    public function guard($value, Field $field): void
    {
        Assertion::integer($value, null, $field->getName());
        $intMin = $this->getMin();
        $intMax = $this->getMax();
        $min = NumberUtils::bound($field->getMin(), $intMin, $intMax);
        $max = NumberUtils::bound($field->getMax(), $intMin, $intMax);
        Assertion::range($value, $min, $max, null, $field->getName());
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
