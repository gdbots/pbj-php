<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\TimeUuidIdentifier;
use Gdbots\Pbj\Message;
use Gdbots\Pbj\MessageRef;

interface Event extends Message
{
    const EVENT_ID_FIELD_NAME = 'event_id';
    const MICROTIME_FIELD_NAME = 'microtime';
    const CORRELATOR_FIELD_NAME = 'correlator';

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
