<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

final class SignedTinyIntType extends AbstractIntType
{
    public function getMin(): int
    {
        return -128;
    }

    public function getMax(): int
    {
        return 127;
    }
}
