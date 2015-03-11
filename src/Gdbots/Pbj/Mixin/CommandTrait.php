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
trait CommandTrait
{
    /**
     * @return bool
     */
    public function hasCommandId()
    {
        return $this->has(Command::COMMAND_ID_FIELD_NAME);
    }

    /**
     * @return TimeUuidIdentifier
     */
    public function getCommandId()
    {
        return $this->get(Command::COMMAND_ID_FIELD_NAME);
    }

    /**
     * @param TimeUuidIdentifier $id
     * @return static
     */
    public function setCommandId(TimeUuidIdentifier $id)
    {
        return $this->setSingleValue(Command::COMMAND_ID_FIELD_NAME, $id);
    }

    /**
     * @return bool
     */
    public function hasMicrotime()
    {
        return $this->has(Command::MICROTIME_FIELD_NAME);
    }

    /**
     * @return Microtime
     */
    public function getMicrotime()
    {
        return $this->get(Command::MICROTIME_FIELD_NAME);
    }

    /**
     * @param Microtime $microtime
     * @return static
     */
    public function setMicrotime(Microtime $microtime)
    {
        return $this->setSingleValue(Command::MICROTIME_FIELD_NAME, $microtime);
    }
}
