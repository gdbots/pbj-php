<?php

namespace Gdbots\Pbj\Type;

final class TinyIntType extends AbstractIntType
{
    /**
     * {@inheritdoc}
     */
    public function getMin()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getMax()
    {
        return 255;
    }
}
