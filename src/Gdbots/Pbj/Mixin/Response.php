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
    const REQUEST_ID_FIELD_NAME = 'request_id';
    const CORRELATOR_FIELD_NAME = 'correlator';

    /**
     * @return bool
     */
    public function hasResponseId();

    /**
     * @return UuidIdentifier
     */
    public function getResponseId();

    /**
     * @param UuidIdentifier $id
     * @return static
     */
    public function setResponseId(UuidIdentifier $id);

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
    public function hasRequestId();

    /**
     * @return UuidIdentifier
     */
    public function getRequestId();

    /**
     * @param UuidIdentifier $id
     * @return static
     */
    public function setRequestId(UuidIdentifier $id);

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
