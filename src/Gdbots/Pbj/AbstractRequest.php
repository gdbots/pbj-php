<?php

namespace Gdbots\Pbj;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;

abstract class AbstractRequest extends AbstractMessage implements Request
{
    /**
     * {@inheritdoc}
     */
    final public function hasRequestId()
    {
        return $this->has(RequestSchema::REQUEST_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getRequestId()
    {
        return $this->get(RequestSchema::REQUEST_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setRequestId(UuidIdentifier $id)
    {
        return $this->setSingleValue(RequestSchema::REQUEST_ID_FIELD_NAME, $id);
    }

    /**
     * {@inheritdoc}
     */
    final public function hasMicrotime()
    {
        return $this->has(RequestSchema::MICROTIME_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getMicrotime()
    {
        return $this->get(RequestSchema::MICROTIME_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setMicrotime(Microtime $microtime)
    {
        return $this->setSingleValue(RequestSchema::MICROTIME_FIELD_NAME, $microtime);
    }
}
