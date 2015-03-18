<?php

namespace Gdbots\Pbj\Type;

final class BlobType extends AbstractBinaryType
{
    /**
     * {@inheritdoc}
     */
    public function allowedInSet()
    {
        return false;
    }
}
