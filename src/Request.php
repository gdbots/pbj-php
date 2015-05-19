<?php

namespace Gdbots\Pbj;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;

interface Request extends Message
{
    /**
     * @param string $tag
     * @return MessageRef
     */
    public function generateMessageRef($tag = null);

    /**
     * @return bool
     */
    public function hasRequestId();

    /**
     * @return UuidIdentifier
     */
    public function getRequestId();

    /**
     * @param UuidIdentifier $requestId
     * @return static
     */
    public function setRequestId(UuidIdentifier $requestId);

    /**
     * @return static
     */
    public function clearRequestId();

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
