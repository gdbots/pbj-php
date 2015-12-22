<?php

namespace Gdbots\Pbj;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\TimeUuidIdentifier;

interface DomainCommand extends Message
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

    /**
     * @return int
     */
    public function getRetries();

    /**
     * @param int $retries
     * @return static
     */
    public function setRetries($retries = 0);

    /**
     * @return static
     */
    public function clearRetries();
}
