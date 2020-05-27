<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

final class SignedIntType extends AbstractIntType
{
    public function getMin(): int
    {
        return -2147483648;
    }

    public function getMax(): int
    {
        return 2147483647;
    }
}
