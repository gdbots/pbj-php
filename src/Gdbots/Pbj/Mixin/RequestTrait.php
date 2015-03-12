<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\Schema;

/**
 * @method static Schema schema()
 * @method bool has(string $fieldName)
 * @method mixed get(string $fieldName)
 * @method static setSingleValue(string $fieldName, mixed $value)
 */
trait RequestTrait
{
    /**
     * @return bool
     */
    public function hasRequestId()
    {
        return $this->has(Request::REQUEST_ID_FIELD_NAME);
    }

    /**
     * @return UuidIdentifier
     */
    public function getRequestId()
    {
        return $this->get(Request::REQUEST_ID_FIELD_NAME);
    }

    /**
     * @param UuidIdentifier $id
     * @return static
     */
    public function setRequestId(UuidIdentifier $id)
    {
        return $this->setSingleValue(Request::REQUEST_ID_FIELD_NAME, $id);
    }

    /**
     * @return bool
     */
    public function hasMicrotime()
    {
        return $this->has(Request::MICROTIME_FIELD_NAME);
    }

    /**
     * @return Microtime
     */
    public function getMicrotime()
    {
        return $this->get(Request::MICROTIME_FIELD_NAME);
    }

    /**
     * @param Microtime $microtime
     * @return static
     */
    public function setMicrotime(Microtime $microtime)
    {
        return $this->setSingleValue(Request::MICROTIME_FIELD_NAME, $microtime);
    }
}
