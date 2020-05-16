<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

final class MediumBlobType extends AbstractBinaryType
{
    public function getMaxBytes(): int
    {
        return 16777215;
    }

    public function allowedInSet(): bool
    {
        return false;
    }
}
