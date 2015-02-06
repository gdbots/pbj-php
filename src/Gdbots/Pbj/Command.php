<?php

namespace Gdbots\Pbj;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\TimeUuidIdentifier;

interface Command extends Message
{
    /**
     * @return bool
     */
    public function hasCommandId();

    /**
     * @return TimeUuidIdentifier
     */
    public function getCommandId();

    /**
     * @param TimeUuidIdentifier $id
     * @return static
     */
    public function setCommandId(TimeUuidIdentifier $id);

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
}
