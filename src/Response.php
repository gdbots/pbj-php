<?php

namespace Gdbots\Pbj;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;

interface Response extends Message
{
    /**
     * @param string $tag
     * @return MessageRef
     */
    public function generateMessageRef($tag = null);

    /**
     * @return bool
     */
    public function hasResponseId();

    /**
     * @return UuidIdentifier
     */
    public function getResponseId();

    /**
     * @param UuidIdentifier $responseId
     * @return static
     */
    public function setResponseId(UuidIdentifier $responseId);

    /**
     * @return static
     */
    public function clearResponseId();

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
    public function hasRequestRef();

    /**
     * @return MessageRef
     */
    public function getRequestRef();

    /**
     * @param MessageRef $requestRef
     * @return static
     */
    public function setRequestRef(MessageRef $requestRef);

    /**
     * @return static
     */
    public function clearRequestRef();

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
