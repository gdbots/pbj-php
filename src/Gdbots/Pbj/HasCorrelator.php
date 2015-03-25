<?php

namespace Gdbots\Pbj;

interface HasCorrelator
{
    /**
     * Copies a correlator from another message if the message is not frozen.
     *
     * @param HasCorrelator $other
     * @return static
     */
    public function copyCorrelator(HasCorrelator $other);

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
