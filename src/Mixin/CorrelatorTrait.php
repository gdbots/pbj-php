<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Pbj\MessageRef;

trait CorrelatorTrait
{
    /**
     * @return bool
     */
    public function hasCorrelator()
    {
        return $this->has('correlator');
    }

    /**
     * @return MessageRef
     */
    public function getCorrelator()
    {
        return $this->get('correlator');
    }

    /**
     * @param MessageRef $correlator
     * @return static
     */
    public function setCorrelator(MessageRef $correlator)
    {
        return $this->setSingleValue('correlator', $correlator);
    }

    /**
     * @return static
     */
    public function clearCorrelator()
    {
        return $this->clear('correlator');
    }
}
