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
}
