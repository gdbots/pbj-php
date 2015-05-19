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
trait RequestTrait
{
    use CorrelatorTrait;
    use MicrotimeTrait;

    /**
     * @param string $tag
     * @return MessageRef
     */
    public function generateMessageRef($tag = null)
    {
        return new MessageRef(static::schema()->getCurie(), $this->getRequestId(), $tag);
    }

    /**
     * @return bool
     */
    public function hasRequestId()
    {
        return $this->has('request_id');
    }

    /**
     * @return UuidIdentifier
     */
    public function getRequestId()
    {
        return $this->get('request_id');
    }

    /**
     * @param UuidIdentifier $requestId
     * @return static
     */
    public function setRequestId(UuidIdentifier $requestId)
    {
        return $this->setSingleValue('request_id', $requestId);
    }

    /**
     * @return static
     */
    public function clearRequestId()
    {
        return $this->clear('request_id');
    }
}
