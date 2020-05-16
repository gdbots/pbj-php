<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

final class TextType extends AbstractStringType
{
    public function allowedInSet(): bool
    {
        return false;
    }
}
