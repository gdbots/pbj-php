<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

final class StringType extends AbstractStringType
{
    public function getMaxBytes(): int
    {
        return 255;
    }
}
