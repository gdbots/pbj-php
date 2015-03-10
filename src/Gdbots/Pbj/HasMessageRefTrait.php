<?php

namespace Gdbots\Pbj;

use Gdbots\Identifiers\UuidIdentifier;

/**
 * @method static Schema schema()
 */
trait HasMessageRefTrait
{
    /**
     * @return MessageRef
     */
    public function getMessageRef()
    {
        return new MessageRef($this::schema()->getCurie(), $this->getMessageId());
    }

    /**
     * @return UuidIdentifier
     */
    abstract public function getMessageId();
}
