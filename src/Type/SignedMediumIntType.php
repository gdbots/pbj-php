<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

final class SignedMediumIntType extends AbstractIntType
{
    public function getMin(): int
    {
        return -8388608;
    }

    public function getMax(): int
    {
        return 8388607;
    }
}
