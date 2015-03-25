<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\GeneratesMessageRef;
use Gdbots\Pbj\GeneratesMessageRefTrait;
use Gdbots\Pbj\HasCorrelator;
use Gdbots\Pbj\HasCorrerlatorTrait;
use Gdbots\Pbj\MessageRef;

abstract class AbstractResponse extends AbstractMessage implements GeneratesMessageRef, HasCorrelator, Response
{
    use GeneratesMessageRefTrait;
    use HasCorrerlatorTrait;

    /**
     * {@inheritdoc}
     */
    final public function getMessageId()
    {
        return $this->getResponseId();
    }

    /**
     * @return bool
     */
    final public function hasResponseId()
    {
        return $this->has(Response::RESPONSE_ID_FIELD_NAME);
    }

    /**
     * @return UuidIdentifier
     */
    final public function getResponseId()
    {
        return $this->get(Response::RESPONSE_ID_FIELD_NAME);
    }

    /**
     * @param UuidIdentifier $id
     * @return static
     */
    final public function setResponseId(UuidIdentifier $id)
    {
        return $this->setSingleValue(Response::RESPONSE_ID_FIELD_NAME, $id);
    }

    /**
     * @return bool
     */
    final public function hasMicrotime()
    {
        return $this->has(Response::MICROTIME_FIELD_NAME);
    }

    /**
     * @return Microtime
     */
    final public function getMicrotime()
    {
        return $this->get(Response::MICROTIME_FIELD_NAME);
    }

    /**
     * @param Microtime $microtime
     * @return static
     */
    final public function setMicrotime(Microtime $microtime)
    {
        return $this->setSingleValue(Response::MICROTIME_FIELD_NAME, $microtime);
    }

    /**
     * @return bool
     */
    final public function hasRequestRef()
    {
        return $this->has(Response::REQUEST_REF_FIELD_NAME);
    }

    /**
     * @return MessageRef
     */
    final public function getRequestRef()
    {
        return $this->get(Response::REQUEST_REF_FIELD_NAME);
    }

    /**
     * @param MessageRef $requestRef
     * @return static
     */
    final public function setRequestRef(MessageRef $requestRef)
    {
        return $this->setSingleValue(Response::REQUEST_REF_FIELD_NAME, $requestRef);
    }
}
