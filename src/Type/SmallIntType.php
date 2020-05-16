<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

final class SmallIntType extends AbstractIntType
{
    public function getMin(): int
    {
        return 0;
    }

    public function getMax(): int
    {
        return 65535;
    }
}
