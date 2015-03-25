<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\MessageRef;

abstract class AbstractResponse extends AbstractMessage implements Response
{
    use CorrelatorTrait;
    use MicrotimeTrait;

    /**
     * @param string $tag
     * @return MessageRef
     */
    final public function generateMessageRef($tag = null)
    {
        return new MessageRef(static::schema()->getCurie(), $this->getResponseId(), $tag);
    }

    /**
     * @return UuidIdentifier
     */
    final public function getResponseId()
    {
        return $this->get(Response::RESPONSE_ID_FIELD_NAME);
    }

    /**
     * @param UuidIdentifier $id
     * @return static
     */
    final public function setResponseId(UuidIdentifier $id)
    {
        return $this->setSingleValue(Response::RESPONSE_ID_FIELD_NAME, $id);
    }

    /**
     * @return bool
     */
    final public function hasRequestRef()
    {
        return $this->has(Response::REQUEST_REF_FIELD_NAME);
    }

    /**
     * @return MessageRef
     */
    final public function getRequestRef()
    {
        return $this->get(Response::REQUEST_REF_FIELD_NAME);
    }

    /**
     * @param MessageRef $requestRef
     * @return static
     */
    final public function setRequestRef(MessageRef $requestRef)
    {
        return $this->setSingleValue(Response::REQUEST_REF_FIELD_NAME, $requestRef);
    }
}
