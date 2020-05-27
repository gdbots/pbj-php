<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

final class BlobType extends AbstractBinaryType
{
    public function allowedInSet(): bool
    {
        return false;
    }
}
