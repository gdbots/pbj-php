<?php

namespace Gdbots\Pbj\Extension;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\AbstractMessage;

abstract class AbstractResponse extends AbstractMessage implements Response
{
    /**
     * {@inheritdoc}
     */
    final public function hasResponseId()
    {
        return $this->has(ResponseSchema::RESPONSE_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getResponseId()
    {
        return $this->get(ResponseSchema::RESPONSE_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setResponseId(UuidIdentifier $id)
    {
        return $this->setSingleValue(ResponseSchema::RESPONSE_ID_FIELD_NAME, $id);
    }

    /**
     * {@inheritdoc}
     */
    final public function hasMicrotime()
    {
        return $this->has(ResponseSchema::MICROTIME_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getMicrotime()
    {
        return $this->get(ResponseSchema::MICROTIME_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setMicrotime(Microtime $microtime)
    {
        return $this->setSingleValue(ResponseSchema::MICROTIME_FIELD_NAME, $microtime);
    }

    /**
     * {@inheritdoc}
     */
    final public function hasRequestId()
    {
        return $this->has(ResponseSchema::REQUEST_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getRequestId()
    {
        return $this->get(ResponseSchema::REQUEST_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setRequestId(UuidIdentifier $id)
    {
        return $this->setSingleValue(ResponseSchema::REQUEST_ID_FIELD_NAME, $id);
    }
}
