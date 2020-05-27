<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Message;

final class FrozenMessageIsImmutable extends \LogicException implements GdbotsPbjException
{
    private Message $type;

    public function __construct(Message $type)
    {
        $this->type = $type;
        parent::__construct('Message is frozen and cannot be modified.');
    }

    public function getType(): Message
    {
        return $this->type;
    }
}
