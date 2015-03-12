<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\GeneratesMessageRef;
use Gdbots\Pbj\GeneratesMessageRefTrait;
use Gdbots\Pbj\HasCorrelator;
use Gdbots\Pbj\HasCorrerlatorTrait;

// todo: attempts/retries transient fields?  or transient fields bag?
abstract class AbstractCommand extends AbstractMessage implements Command, GeneratesMessageRef, HasCorrelator
{
    use CommandTrait;
    use GeneratesMessageRefTrait;
    use HasCorrerlatorTrait;

    /**
     * {@inheritdoc}
     */
    final public function getMessageId()
    {
        return $this->getCommandId();
    }
}
