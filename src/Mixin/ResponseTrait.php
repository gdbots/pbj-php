<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\MessageRef;

trait ResponseTrait
{
    use MessageTrait;

    /**
     * @param string $tag
     * @return MessageRef
     */
    public function generateMessageRef($tag = null)
    {
        return new MessageRef(static::schema()->getCurie(), $this->getResponseId(), $tag);
    }

    /**
     * @return array
     */
    public function getUriTemplateVars()
    {
        return [
            'response_id' => $this->getResponseId()->toString(),
            'microtime' => $this->getMicrotime()->toString(),
        ];
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
}
