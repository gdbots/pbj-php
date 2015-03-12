<?php

namespace Gdbots\Pbj;

use Gdbots\Identifiers\UuidIdentifier;

/**
 * @method static Schema schema()
 */
trait GeneratesMessageRefTrait
{
    /**
     * @see GeneratesMessageRef::getMessageRef
     *
     * @param string $tag
     * @return MessageRef
     */
    public function generateMessageRef($tag = null)
    {
        return new MessageRef(static::schema()->getCurie(), $this->getMessageId(), $tag);
    }

    /**
     * @return UuidIdentifier
     */
    abstract public function getMessageId();
}
