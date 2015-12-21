<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\TimeUuidIdentifier;
use Gdbots\Pbj\MessageRef;

trait CommandTrait
{
    use MessageTrait;

    /**
     * @param string $tag
     * @return MessageRef
     */
    public function generateMessageRef($tag = null)
    {
        return new MessageRef(static::schema()->getCurie(), $this->getCommandId(), $tag);
    }

    /**
     * @return array
     */
    public function getUriTemplateVars()
    {
        return [
            'command_id' => $this->getCommandId()->toString(),
            'microtime' => $this->getMicrotime()->toString(),
        ];
    }

    /**
     * @return bool
     */
    public function hasCommandId()
    {
        return $this->has('command_id');
    }

    /**
     * @return TimeUuidIdentifier
     */
    public function getCommandId()
    {
        return $this->get('command_id');
    }

    /**
     * @param TimeUuidIdentifier $commandId
     * @return static
     */
    public function setCommandId(TimeUuidIdentifier $commandId)
    {
        return $this->setSingleValue('command_id', $commandId);
    }

    /**
     * @return static
     */
    public function clearCommandId()
    {
        return $this->clear('command_id');
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

    /**
     * @return int
     */
    public function getRetries()
    {
        return $this->get('retries');
    }

    /**
     * @param int $retries
     * @return static
     */
    public function setRetries($retries = 0)
    {
        return $this->setSingleValue('retries', (int) $retries);
    }

    /**
     * @return static
     */
    public function clearRetries()
    {
        return $this->clear('retries');
    }
}
