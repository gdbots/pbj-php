<?php

namespace Gdbots\Pbj\Extension;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\TimeUuidIdentifier;
use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\HasMessageRef;
use Gdbots\Pbj\MessageRef;

// todo: attempts/retries transient fields?  or transparent fields bag?
abstract class AbstractCommand extends AbstractMessage implements Command, HasMessageRef
{
    /** @var MessageRef */
    private $messageRef;

    /**
     * {@inheritdoc}
     */
    final public function getMessageRef()
    {
        if (null === $this->messageRef) {
            $this->messageRef = new MessageRef($this::schema()->getCurie(), $this->getCommandId());
        }
        return $this->messageRef;
    }

    /**
     * {@inheritdoc}
     */
    final public function hasCommandId()
    {
        return $this->has(CommandSchema::COMMAND_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getCommandId()
    {
        return $this->get(CommandSchema::COMMAND_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setCommandId(TimeUuidIdentifier $id)
    {
        $this->messageRef = null;
        return $this->setSingleValue(CommandSchema::COMMAND_ID_FIELD_NAME, $id);
    }

    /**
     * {@inheritdoc}
     */
    final public function hasMicrotime()
    {
        return $this->has(CommandSchema::MICROTIME_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getMicrotime()
    {
        return $this->get(CommandSchema::MICROTIME_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setMicrotime(Microtime $microtime)
    {
        return $this->setSingleValue(CommandSchema::MICROTIME_FIELD_NAME, $microtime);
    }

    /**
     * {@inheritdoc}
     */
    final public function hasCorrelator()
    {
        return $this->has(CommandSchema::CORRELATOR_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getCorrelator()
    {
        return $this->get(CommandSchema::CORRELATOR_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setCorrelator(MessageRef $correlator)
    {
        return $this->setSingleValue(CommandSchema::CORRELATOR_FIELD_NAME, $correlator);
    }
}
