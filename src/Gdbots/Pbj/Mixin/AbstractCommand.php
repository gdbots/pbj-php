<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\TimeUuidIdentifier;
use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\GeneratesMessageRef;
use Gdbots\Pbj\GeneratesMessageRefTrait;
use Gdbots\Pbj\HasCorrelator;
use Gdbots\Pbj\HasCorrerlatorTrait;

// todo: attempts/retries transient fields?  or transient fields bag?
abstract class AbstractCommand extends AbstractMessage implements Command, GeneratesMessageRef, HasCorrelator
{
    use GeneratesMessageRefTrait;
    use HasCorrerlatorTrait;

    /**
     * {@inheritdoc}
     */
    final public function getMessageId()
    {
        return $this->getCommandId();
    }

    /**
     * @return bool
     */
    final public function hasCommandId()
    {
        return $this->has(Command::COMMAND_ID_FIELD_NAME);
    }

    /**
     * @return TimeUuidIdentifier
     */
    final public function getCommandId()
    {
        return $this->get(Command::COMMAND_ID_FIELD_NAME);
    }

    /**
     * @param TimeUuidIdentifier $id
     * @return static
     */
    final public function setCommandId(TimeUuidIdentifier $id)
    {
        return $this->setSingleValue(Command::COMMAND_ID_FIELD_NAME, $id);
    }

    /**
     * @return bool
     */
    final public function hasMicrotime()
    {
        return $this->has(Command::MICROTIME_FIELD_NAME);
    }

    /**
     * @return Microtime
     */
    final public function getMicrotime()
    {
        return $this->get(Command::MICROTIME_FIELD_NAME);
    }

    /**
     * @param Microtime $microtime
     * @return static
     */
    final public function setMicrotime(Microtime $microtime)
    {
        return $this->setSingleValue(Command::MICROTIME_FIELD_NAME, $microtime);
    }
}
