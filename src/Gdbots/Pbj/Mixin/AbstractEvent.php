<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Identifiers\TimeUuidIdentifier;
use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\MessageRef;

abstract class AbstractEvent extends AbstractMessage implements DomainEvent
{
    use CorrelatorTrait;
    use MicrotimeTrait;

    /**
     * @param string $tag
     * @return MessageRef
     */
    final public function generateMessageRef($tag = null)
    {
        return new MessageRef(static::schema()->getCurie(), $this->getEventId(), $tag);
    }

    /**
     * @return TimeUuidIdentifier
     */
    final public function getEventId()
    {
        return $this->get(Event::EVENT_ID_FIELD_NAME);
    }

    /**
     * @param TimeUuidIdentifier $id
     * @return static
     */
    final public function setEventId(TimeUuidIdentifier $id)
    {
        return $this->setSingleValue(Event::EVENT_ID_FIELD_NAME, $id);
    }
}
