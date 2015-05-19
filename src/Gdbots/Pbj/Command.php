<?php

namespace Gdbots\Pbj;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\TimeUuidIdentifier;

interface Command extends Message
{
    const COMMAND_ID_FIELD_NAME = 'command_id';
    const MICROTIME_FIELD_NAME = 'microtime';
    const CORRELATOR_FIELD_NAME = 'correlator';

    /**
     * @param string $tag
     * @return MessageRef
     */
    public function generateMessageRef($tag = null);

    /**
     * @return bool
     */
    public function hasCommandId();

    /**
     * @return TimeUuidIdentifier
     */
    public function getCommandId();

    /**
     * @param TimeUuidIdentifier $commandId
     * @return static
     */
    public function setCommandId(TimeUuidIdentifier $commandId);

    /**
     * @return static
     */
    public function clearCommandId();

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
     * @return static
     */
    public function clearMicrotime();

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

    /**
     * @return static
     */
    public function clearCorrelator();
}
