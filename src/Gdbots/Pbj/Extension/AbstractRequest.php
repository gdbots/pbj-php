<?php

namespace Gdbots\Pbj\Extension;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\HasMessageRef;
use Gdbots\Pbj\MessageRef;

abstract class AbstractRequest extends AbstractMessage implements Request, HasMessageRef
{
    /** @var MessageRef */
    private $messageRef;

    /**
     * {@inheritdoc}
     */
    final public function getMessageRef()
    {
        if (null === $this->messageRef) {
            $this->messageRef = new MessageRef($this::schema()->getCurie(), $this->getRequestId());
        }
        return $this->messageRef;
    }

    /**
     * {@inheritdoc}
     */
    final public function hasRequestId()
    {
        return $this->has(RequestSchema::REQUEST_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getRequestId()
    {
        return $this->get(RequestSchema::REQUEST_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setRequestId(UuidIdentifier $id)
    {
        $this->messageRef = null;
        return $this->setSingleValue(RequestSchema::REQUEST_ID_FIELD_NAME, $id);
    }

    /**
     * {@inheritdoc}
     */
    final public function hasMicrotime()
    {
        return $this->has(RequestSchema::MICROTIME_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getMicrotime()
    {
        return $this->get(RequestSchema::MICROTIME_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setMicrotime(Microtime $microtime)
    {
        return $this->setSingleValue(RequestSchema::MICROTIME_FIELD_NAME, $microtime);
    }

    /**
     * {@inheritdoc}
     */
    final public function hasCorrelator()
    {
        return $this->has(RequestSchema::CORRELATOR_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getCorrelator()
    {
        return $this->get(RequestSchema::CORRELATOR_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setCorrelator(MessageRef $correlator)
    {
        return $this->setSingleValue(RequestSchema::CORRELATOR_FIELD_NAME, $correlator);
    }
}
