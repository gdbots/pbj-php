<?php

namespace Gdbots\Pbj\Type;

final class TextType extends AbstractStringType
{
    /**
     * {@inheritdoc}
     */
    public function allowedInSet()
    {
        return false;
    }
}
