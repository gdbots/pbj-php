<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\TimeUuidIdentifier;
use Gdbots\Pbj\Schema;

/**
 * @method static Schema schema()
 * @method bool has(string $fieldName)
 * @method mixed get(string $fieldName)
 * @method static setSingleValue(string $fieldName, mixed $value)
 */
trait EventTrait
{
    /**
     * @return bool
     */
    public function hasEventId()
    {
        return $this->has(Event::EVENT_ID_FIELD_NAME);
    }

    /**
     * @return TimeUuidIdentifier
     */
    public function getEventId()
    {
        return $this->get(Event::EVENT_ID_FIELD_NAME);
    }

    /**
     * @param TimeUuidIdentifier $id
     * @return static
     */
    public function setEventId(TimeUuidIdentifier $id)
    {
        return $this->setSingleValue(Event::EVENT_ID_FIELD_NAME, $id);
    }

    /**
     * @return bool
     */
    public function hasMicrotime()
    {
        return $this->has(Event::MICROTIME_FIELD_NAME);
    }

    /**
     * @return Microtime
     */
    public function getMicrotime()
    {
        return $this->get(Event::MICROTIME_FIELD_NAME);
    }

    /**
     * @param Microtime $microtime
     * @return static
     */
    public function setMicrotime(Microtime $microtime)
    {
        return $this->setSingleValue(Event::MICROTIME_FIELD_NAME, $microtime);
    }
}
