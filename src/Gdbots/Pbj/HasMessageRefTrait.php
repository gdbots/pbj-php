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
        return new MessageRef(static::schema()->getCurie(), $this->getMessageId());
    }

    /**
     * @return UuidIdentifier
     */
    abstract public function getMessageId();
}
