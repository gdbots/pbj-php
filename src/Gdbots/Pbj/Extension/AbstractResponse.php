<?php

namespace Gdbots\Pbj\Extension;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\HasMessageRef;
use Gdbots\Pbj\MessageRef;

abstract class AbstractResponse extends AbstractMessage implements Response, HasMessageRef
{
    /** @var MessageRef */
    private $messageRef;

    /**
     * {@inheritdoc}
     */
    final public function getMessageRef()
    {
        if (null === $this->messageRef) {
            $this->messageRef = new MessageRef($this::schema()->getCurie(), $this->getResponseId());
        }
        return $this->messageRef;
    }

    /**
     * {@inheritdoc}
     */
    final public function hasResponseId()
    {
        return $this->has(ResponseSchema::RESPONSE_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getResponseId()
    {
        return $this->get(ResponseSchema::RESPONSE_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setResponseId(UuidIdentifier $id)
    {
        $this->messageRef = null;
        return $this->setSingleValue(ResponseSchema::RESPONSE_ID_FIELD_NAME, $id);
    }

    /**
     * {@inheritdoc}
     */
    final public function hasMicrotime()
    {
        return $this->has(ResponseSchema::MICROTIME_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getMicrotime()
    {
        return $this->get(ResponseSchema::MICROTIME_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setMicrotime(Microtime $microtime)
    {
        return $this->setSingleValue(ResponseSchema::MICROTIME_FIELD_NAME, $microtime);
    }

    /**
     * {@inheritdoc}
     */
    final public function hasRequestId()
    {
        return $this->has(ResponseSchema::REQUEST_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getRequestId()
    {
        return $this->get(ResponseSchema::REQUEST_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setRequestId(UuidIdentifier $id)
    {
        return $this->setSingleValue(ResponseSchema::REQUEST_ID_FIELD_NAME, $id);
    }

    /**
     * {@inheritdoc}
     */
    final public function hasCorrelator()
    {
        return $this->has(ResponseSchema::CORRELATOR_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getCorrelator()
    {
        return $this->get(ResponseSchema::CORRELATOR_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setCorrelator(MessageRef $correlator)
    {
        return $this->setSingleValue(ResponseSchema::CORRELATOR_FIELD_NAME, $correlator);
    }
}
