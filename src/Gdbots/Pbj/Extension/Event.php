<?php

namespace Gdbots\Pbj\Extension;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\TimeUuidIdentifier;
use Gdbots\Pbj\Message;
use Gdbots\Pbj\MessageRef;

// todo: do we need causation_id?
interface Event extends Message
{
    /**
     * @return bool
     */
    public function hasEventId();

    /**
     * @return TimeUuidIdentifier
     */
    public function getEventId();

    /**
     * @param TimeUuidIdentifier $id
     * @return static
     */
    public function setEventId(TimeUuidIdentifier $id);

    /**
     * @return bool
     */
    public function hasMicrotime();

    /**
     * @return Microtime
     */
    public function getMicrotime();

    /**
     * @param Microtime $microtime
     * @return static
     */
    public function setMicrotime(Microtime $microtime);

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
