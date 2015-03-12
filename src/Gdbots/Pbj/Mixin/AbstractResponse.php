<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\GeneratesMessageRef;
use Gdbots\Pbj\GeneratesMessageRefTrait;
use Gdbots\Pbj\HasCorrelator;
use Gdbots\Pbj\HasCorrerlatorTrait;

abstract class AbstractResponse extends AbstractMessage implements GeneratesMessageRef, HasCorrelator, Response
{
    use GeneratesMessageRefTrait;
    use HasCorrerlatorTrait;
    use ResponseTrait;

    /**
     * {@inheritdoc}
     */
    final public function getMessageId()
    {
        return $this->getResponseId();
    }
}
