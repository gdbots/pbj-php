<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Message;

class FrozenMessageIsImmutable extends \LogicException implements GdbotsPbjException
{
    /** @var Message */
    private $type;

    /**
     * @param Message $type
     */
    public function __construct(Message $type)
    {
        $this->type = $type;
        parent::__construct('Message is frozen and cannot be modified.');
    }

    /**
     * @return Message
     */
    public function getType()
    {
        return $this->type;
    }
}
