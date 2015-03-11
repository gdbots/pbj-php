<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\HasCorrelator;
use Gdbots\Pbj\HasCorrerlatorTrait;
use Gdbots\Pbj\HasMessageRef;
use Gdbots\Pbj\HasMessageRefTrait;

// todo: attempts/retries transient fields?  or transient fields bag?
abstract class AbstractCommand extends AbstractMessage implements Command, HasCorrelator, HasMessageRef
{
    use CommandTrait;
    use HasCorrerlatorTrait;
    use HasMessageRefTrait;

    /**
     * {@inheritdoc}
     */
    public function getMessageId()
    {
        return $this->getCommandId();
    }
}
