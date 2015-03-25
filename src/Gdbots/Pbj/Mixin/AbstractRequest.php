<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\MessageRef;

abstract class AbstractRequest extends AbstractMessage implements Request
{
    use CorrelatorTrait;
    use MicrotimeTrait;

    /**
     * @param string $tag
     * @return MessageRef
     */
    final public function generateMessageRef($tag = null)
    {
        return new MessageRef(static::schema()->getCurie(), $this->getRequestId(), $tag);
    }

    /**
     * @return UuidIdentifier
     */
    final public function getRequestId()
    {
        return $this->get(Request::REQUEST_ID_FIELD_NAME);
    }

    /**
     * @param UuidIdentifier $id
     * @return static
     */
    final public function setRequestId(UuidIdentifier $id)
    {
        return $this->setSingleValue(Request::REQUEST_ID_FIELD_NAME, $id);
    }
}
