<?php

namespace Gdbots\Pbj;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\TimeUuidIdentifier;
use Gdbots\Identifiers\UuidIdentifier;

abstract class AbstractEvent extends AbstractMessage implements DomainEvent
{
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
    final public function hasCorrelId()
    {
        return $this->has(EventSchema::CORREL_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getCorrelId()
    {
        return $this->get(EventSchema::CORREL_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setCorrelId(UuidIdentifier $id)
    {
        return $this->setSingleValue(EventSchema::CORREL_ID_FIELD_NAME, $id);
    }
}
