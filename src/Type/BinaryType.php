<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

final class BinaryType extends AbstractBinaryType
{
    public function getMaxBytes(): int
    {
        return 255;
    }
}
