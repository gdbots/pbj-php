<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

final class MediumIntType extends AbstractIntType
{
    public function getMin(): int
    {
        return 0;
    }

    public function getMax(): int
    {
        return 16777215;
    }
}
