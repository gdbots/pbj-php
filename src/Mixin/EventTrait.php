<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\TimeUuidIdentifier;
use Gdbots\Pbj\MessageRef;

trait EventTrait
{
    use MessageTrait;

    /**
     * @param string $tag
     * @return MessageRef
     */
    public function generateMessageRef($tag = null)
    {
        return new MessageRef(static::schema()->getCurie(), $this->getEventId(), $tag);
    }

    /**
     * @return array
     */
    public function getUriTemplateVars()
    {
        return [
            'event_id' => $this->getEventId()->toString(),
            'microtime' => $this->getMicrotime()->toString(),
        ];
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

    /**
     * @return bool
     */
    public function hasMicrotime()
    {
        return $this->has('microtime');
    }

    /**
     * @return Microtime
     */
    public function getMicrotime()
    {
        return $this->get('microtime');
    }

    /**
     * @param Microtime $microtime
     * @return static
     */
    public function setMicrotime(Microtime $microtime)
    {
        return $this->setSingleValue('microtime', $microtime);
    }

    /**
     * @return static
     */
    public function clearMicrotime()
    {
        return $this->clear('microtime');
    }

    /**
     * @return bool
     */
    public function hasCorrelator()
    {
        return $this->has('correlator');
    }

    /**
     * @return MessageRef
     */
    public function getCorrelator()
    {
        return $this->get('correlator');
    }

    /**
     * @param MessageRef $correlator
     * @return static
     */
    public function setCorrelator(MessageRef $correlator)
    {
        return $this->setSingleValue('correlator', $correlator);
    }

    /**
     * @return static
     */
    public function clearCorrelator()
    {
        return $this->clear('correlator');
    }
}
