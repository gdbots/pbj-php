<?php

namespace Gdbots\Pbj\Type;

final class MediumTextType extends AbstractStringType
{
    /**
     * {@inheritdoc}
     */
    public function getMaxBytes()
    {
        return 16777215;
    }

    /**
     * {@inheritdoc}
     */
    public function allowedInSet()
    {
        return false;
    }
}
