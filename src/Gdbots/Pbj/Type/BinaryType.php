<?php

namespace Gdbots\Pbj\Type;

final class BinaryType extends AbstractBinaryType
{
    /**
     * {@inheritdoc}
     */
    public function getMaxBytes()
    {
        return 255;
    }

    /**
     * {@inheritdoc}
     */
    public function allowedInSetOrList()
    {
        return true;
    }
}
