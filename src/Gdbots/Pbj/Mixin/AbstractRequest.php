<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\GeneratesMessageRef;
use Gdbots\Pbj\GeneratesMessageRefTrait;
use Gdbots\Pbj\HasCorrelator;
use Gdbots\Pbj\HasCorrerlatorTrait;

abstract class AbstractRequest extends AbstractMessage implements GeneratesMessageRef, HasCorrelator, Request
{
    use GeneratesMessageRefTrait;
    use HasCorrerlatorTrait;
    use RequestTrait;

    /**
     * {@inheritdoc}
     */
    final public function getMessageId()
    {
        return $this->getRequestId();
    }
}
