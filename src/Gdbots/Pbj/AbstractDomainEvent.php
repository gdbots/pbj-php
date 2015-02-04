<?php

namespace Gdbots\Pbj;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\TimeUuidIdentifier;

abstract class AbstractDomainEvent extends AbstractMessage implements DomainEvent
{
    /**
     * {@inheritdoc}
     */
    final public function getEventId()
    {
        return $this->get(EventSchema::EVENT_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function setEventId(TimeUuidIdentifier $id)
    {
        return $this->setSingleValue(EventSchema::EVENT_ID_FIELD_NAME, $id);
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
     */
    final public function setMicrotime(Microtime $microtime)
    {
        return $this->setSingleValue(EventSchema::MICROTIME_FIELD_NAME, $microtime);
    }
}
