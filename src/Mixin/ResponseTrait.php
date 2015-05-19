<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\MessageRef;
use Gdbots\Pbj\Schema;

/**
 * @method Schema schema()
 * @method bool has(string $fieldName)
 * @method mixed get(string $fieldName)
 * @method static setSingleValue(string $fieldName, mixed $value)
 * @method static clear(string $fieldName)
 */
trait ResponseTrait
{
    use CorrelatorTrait;
    use MicrotimeTrait;

    /**
     * @param string $tag
     * @return MessageRef
     */
    public function generateMessageRef($tag = null)
    {
        return new MessageRef(static::schema()->getCurie(), $this->getResponseId(), $tag);
    }

    /**
     * @return bool
     */
    public function hasResponseId()
    {
        return $this->has('response_id');
    }

    /**
     * @return UuidIdentifier
     */
    public function getResponseId()
    {
        return $this->get('response_id');
    }

    /**
     * @param UuidIdentifier $responseId
     * @return static
     */
    public function setResponseId(UuidIdentifier $responseId)
    {
        return $this->setSingleValue('response_id', $responseId);
    }

    /**
     * @return static
     */
    public function clearResponseId()
    {
        return $this->clear('response_id');
    }

    /**
     * @return bool
     */
    public function hasRequestRef()
    {
        return $this->has('request_ref');
    }

    /**
     * @return MessageRef
     */
    public function getRequestRef()
    {
        return $this->get('request_ref');
    }

    /**
     * @param MessageRef $requestRef
     * @return static
     */
    public function setRequestRef(MessageRef $requestRef)
    {
        return $this->setSingleValue('request_ref', $requestRef);
    }

    /**
     * @return static
     */
    public function clearRequestRef()
    {
        return $this->clear('request_ref');
    }
}
