<?php

namespace Gdbots\Pbj\Type;

final class SmallIntType extends AbstractIntType
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
        return 65535;
    }
}
