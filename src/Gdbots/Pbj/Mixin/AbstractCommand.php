<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Identifiers\TimeUuidIdentifier;
use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\MessageRef;

// todo: attempts/retries transient fields?  or transient fields bag?
abstract class AbstractCommand extends AbstractMessage implements Command
{
    use CorrelatorTrait;
    use MicrotimeTrait;

    /**
     * @param string $tag
     * @return MessageRef
     */
    final public function generateMessageRef($tag = null)
    {
        return new MessageRef(static::schema()->getCurie(), $this->getCommandId(), $tag);
    }

    /**
     * @return TimeUuidIdentifier
     */
    final public function getCommandId()
    {
        return $this->get(Command::COMMAND_ID_FIELD_NAME);
    }

    /**
     * @param TimeUuidIdentifier $commandId
     * @return static
     */
    final public function setCommandId(TimeUuidIdentifier $commandId)
    {
        return $this->setSingleValue(Command::COMMAND_ID_FIELD_NAME, $commandId);
    }
}
