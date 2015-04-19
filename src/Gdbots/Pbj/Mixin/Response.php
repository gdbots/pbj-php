<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\Message;
use Gdbots\Pbj\MessageRef;

interface Response extends Message
{
    const RESPONSE_ID_FIELD_NAME = 'response_id';
    const MICROTIME_FIELD_NAME = 'microtime';
    const REQUEST_REF_FIELD_NAME = 'request_ref';
    const CORRELATOR_FIELD_NAME = 'correlator';

    /**
     * @param string $tag
     * @return MessageRef
     */
    public function generateMessageRef($tag = null);

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
