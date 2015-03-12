<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\GeneratesMessageRef;
use Gdbots\Pbj\GeneratesMessageRefTrait;
use Gdbots\Pbj\HasCorrelator;
use Gdbots\Pbj\HasCorrerlatorTrait;

abstract class AbstractEvent extends AbstractMessage implements DomainEvent, GeneratesMessageRef, HasCorrelator
{
    use EventTrait;
    use GeneratesMessageRefTrait;
    use HasCorrerlatorTrait;

    /**
     * {@inheritdoc}
     */
    final public function getMessageId()
    {
        return $this->getEventId();
    }
}
