<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\MessageRef;

trait EntityTrait
{
    /**
     * @param string $tag
     * @return MessageRef
     */
    public function generateMessageRef($tag = null)
    {
        return new MessageRef(static::schema()->getCurie(), $this->getEntityId(), $tag);
    }

    /**
     * @return bool
     */
    public function hasEntityId()
    {
        return $this->has('_id');
    }

    /**
     * @return UuidIdentifier
     */
    public function getEntityId()
    {
        return $this->get('_id');
    }

    /**
     * @param UuidIdentifier $entityId
     * @return static
     */
    public function setEntityId(UuidIdentifier $entityId)
    {
        return $this->setSingleValue('_id', $entityId);
    }

    /**
     * @return static
     */
    public function clearEntityId()
    {
        return $this->clear('_id');
    }

    /**
     * @return bool
     */
    public function hasEtag()
    {
        return $this->has('etag');
    }

    /**
     * @return string
     */
    public function getEtag()
    {
        return $this->get('etag');
    }

    /**
     * @param string $etag
     * @return static
     */
    public function setEtag($etag)
    {
        return $this->setSingleValue('etag', $etag);
    }

    /**
     * @return static
     */
    public function clearEtag()
    {
        return $this->clear('etag');
    }

    /**
     * @return bool
     */
    public function hasCreatedAt()
    {
        return $this->has('created_at');
    }

    /**
     * @return Microtime
     */
    public function getCreatedAt()
    {
        return $this->get('created_at');
    }

    /**
     * @param Microtime $createdAt
     * @return static
     */
    public function setCreatedAt(Microtime $createdAt)
    {
        return $this->setSingleValue('created_at', $createdAt);
    }

    /**
     * @return static
     */
    public function clearCreatedAt()
    {
        return $this->clear('created_at');
    }

    /**
     * @return bool
     */
    public function hasUpdatedAt()
    {
        return $this->has('updated_at');
    }

    /**
     * @return Microtime
     */
    public function getUpdatedAt()
    {
        return $this->get('updated_at');
    }

    /**
     * @param Microtime $updatedAt
     * @return static
     */
    public function setUpdatedAt(Microtime $updatedAt)
    {
        return $this->setSingleValue('updated_at', $updatedAt);
    }

    /**
     * @return static
     */
    public function clearUpdatedAt()
    {
        return $this->clear('updated_at');
    }
}
