<?php

namespace Gdbots\Pbj\Type;

final class MediumBlobType extends AbstractBinaryType
{
    /**
     * {@inheritdoc}
     */
    public function getMaxBytes()
    {
        return 16777215;
    }
}
