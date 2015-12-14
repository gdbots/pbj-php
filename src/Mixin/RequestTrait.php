<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\MessageRef;

trait RequestTrait
{
    use MessageTrait;

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

    /**
     * @return bool
     */
    public function hasMicrotime()
    {
        return $this->has('microtime');
    }

    /**
     * @return Microtime
     */
    public function getMicrotime()
    {
        return $this->get('microtime');
    }

    /**
     * @param Microtime $microtime
     * @return static
     */
    public function setMicrotime(Microtime $microtime)
    {
        return $this->setSingleValue('microtime', $microtime);
    }

    /**
     * @return static
     */
    public function clearMicrotime()
    {
        return $this->clear('microtime');
    }

    /**
     * @return bool
     */
    public function hasCorrelator()
    {
        return $this->has('correlator');
    }

    /**
     * @return MessageRef
     */
    public function getCorrelator()
    {
        return $this->get('correlator');
    }

    /**
     * @param MessageRef $correlator
     * @return static
     */
    public function setCorrelator(MessageRef $correlator)
    {
        return $this->setSingleValue('correlator', $correlator);
    }

    /**
     * @return static
     */
    public function clearCorrelator()
    {
        return $this->clear('correlator');
    }

    /**
     * @return int
     */
    public function getRetries()
    {
        return $this->get('retries');
    }

    /**
     * @param int $retries
     * @return static
     */
    public function setRetries($retries = 0)
    {
        return $this->setSingleValue('retries', (int) $retries);
    }

    /**
     * @return static
     */
    public function clearRetries()
    {
        return $this->clear('retries');
    }
}
