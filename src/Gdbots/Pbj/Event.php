<?php

namespace Gdbots\Pbj;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\TimeUuidIdentifier;
use Gdbots\Identifiers\UuidIdentifier;

interface Event extends Message
{
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
    public function hasCorrelId();

    /**
     * @return UuidIdentifier
     */
    public function getCorrelId();

    /**
     * @param UuidIdentifier $id
     * @return static
     */
    public function setCorrelId(UuidIdentifier $id);
}
