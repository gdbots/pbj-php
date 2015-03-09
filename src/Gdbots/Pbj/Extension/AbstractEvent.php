<?php

namespace Gdbots\Pbj\Extension;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\TimeUuidIdentifier;
use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\HasMessageRef;
use Gdbots\Pbj\MessageRef;

abstract class AbstractEvent extends AbstractMessage implements DomainEvent, HasMessageRef
{
    /** @var MessageRef */
    private $messageRef;

    /**
     * {@inheritdoc}
     */
    final public function getMessageRef()
    {
        if (null === $this->messageRef) {
            $this->messageRef = new MessageRef($this::schema()->getCurie(), $this->getEventId());
        }
        return $this->messageRef;
    }

    /**
     * {@inheritdoc}
     */
    final public function hasEventId()
    {
        return $this->has(EventSchema::EVENT_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getEventId()
    {
        return $this->get(EventSchema::EVENT_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setEventId(TimeUuidIdentifier $id)
    {
        $this->messageRef = null;
        return $this->setSingleValue(EventSchema::EVENT_ID_FIELD_NAME, $id);
    }

    /**
     * {@inheritdoc}
     */
    final public function hasMicrotime()
    {
        return $this->has(EventSchema::MICROTIME_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getMicrotime()
    {
        return $this->get(EventSchema::MICROTIME_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setMicrotime(Microtime $microtime)
    {
        return $this->setSingleValue(EventSchema::MICROTIME_FIELD_NAME, $microtime);
    }

    /**
     * {@inheritdoc}
     */
    final public function hasCorrelator()
    {
        return $this->has(EventSchema::CORRELATOR_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getCorrelator()
    {
        return $this->get(EventSchema::CORRELATOR_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setCorrelator(MessageRef $correlator)
    {
        return $this->setSingleValue(EventSchema::CORRELATOR_FIELD_NAME, $correlator);
    }
}
