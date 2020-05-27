<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

final class SignedSmallIntType extends AbstractIntType
{
    public function getMin(): int
    {
        return -32768;
    }

    public function getMax(): int
    {
        return 32767;
    }
}
