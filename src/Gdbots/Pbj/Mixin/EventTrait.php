<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Identifiers\TimeUuidIdentifier;
use Gdbots\Pbj\MessageRef;
use Gdbots\Pbj\Schema;

/**
 * @method Schema schema()
 * @method bool has(string $fieldName)
 * @method mixed get(string $fieldName)
 * @method static setSingleValue(string $fieldName, mixed $value)
 * @method static clear(string $fieldName)
 */
trait EventTrait
{
    use CorrelatorTrait;
    use MicrotimeTrait;

    /**
     * @param string $tag
     * @return MessageRef
     */
    public function generateMessageRef($tag = null)
    {
        return new MessageRef(static::schema()->getCurie(), $this->getEventId(), $tag);
    }

    /**
     * @return bool
     */
    public function hasEventId()
    {
        return $this->has('event_id');
    }

    /**
     * @return TimeUuidIdentifier
     */
    public function getEventId()
    {
        return $this->get('event_id');
    }

    /**
     * @param TimeUuidIdentifier $eventId
     * @return static
     */
    public function setEventId(TimeUuidIdentifier $eventId)
    {
        return $this->setSingleValue('event_id', $eventId);
    }

    /**
     * @return static
     */
    public function clearEventId()
    {
        return $this->clear('event_id');
    }
}
