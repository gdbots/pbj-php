<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\GeneratesMessageRef;
use Gdbots\Pbj\GeneratesMessageRefTrait;
use Gdbots\Pbj\HasCorrelator;
use Gdbots\Pbj\HasCorrerlatorTrait;

abstract class AbstractRequest extends AbstractMessage implements GeneratesMessageRef, HasCorrelator, Request
{
    use GeneratesMessageRefTrait;
    use HasCorrerlatorTrait;

    /**
     * {@inheritdoc}
     */
    final public function getMessageId()
    {
        return $this->getRequestId();
    }

    /**
     * @return bool
     */
    final public function hasRequestId()
    {
        return $this->has(Request::REQUEST_ID_FIELD_NAME);
    }

    /**
     * @return UuidIdentifier
     */
    final public function getRequestId()
    {
        return $this->get(Request::REQUEST_ID_FIELD_NAME);
    }

    /**
     * @param UuidIdentifier $id
     * @return static
     */
    final public function setRequestId(UuidIdentifier $id)
    {
        return $this->setSingleValue(Request::REQUEST_ID_FIELD_NAME, $id);
    }

    /**
     * @return bool
     */
    final public function hasMicrotime()
    {
        return $this->has(Request::MICROTIME_FIELD_NAME);
    }

    /**
     * @return Microtime
     */
    final public function getMicrotime()
    {
        return $this->get(Request::MICROTIME_FIELD_NAME);
    }

    /**
     * @param Microtime $microtime
     * @return static
     */
    final public function setMicrotime(Microtime $microtime)
    {
        return $this->setSingleValue(Request::MICROTIME_FIELD_NAME, $microtime);
    }
}
