<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Identifiers\TimeUuidIdentifier;
use Gdbots\Pbj\MessageRef;

// todo: attempts/retries transient fields?  or transient fields bag?
trait CommandTrait
{
    use MessageTrait;
    use CorrelatorTrait;
    use MicrotimeTrait;

    /**
     * @param string $tag
     * @return MessageRef
     */
    public function generateMessageRef($tag = null)
    {
        return new MessageRef(static::schema()->getCurie(), $this->getCommandId(), $tag);
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
}
