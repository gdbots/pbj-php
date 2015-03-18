<?php

namespace Gdbots\Pbj;

interface HasCorrelator
{
    /**
     * Correlates a message to another that also has a correlator with
     * an optional tag to qualify the reference.
     *
     * @param HasCorrelator $other
     * @param string $tag
     * @return static
     */
    public function correlateWith(HasCorrelator $other, $tag = null);

    /**
     * @return bool
     */
    public function hasCorrelator();

    /**
     * @return MessageRef
     */
    public function getCorrelator();

    /**
     * @param MessageRef $correlator
     * @return static
     */
    public function setCorrelator(MessageRef $correlator);
}
