<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\Identifier;
use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\MessageRef;

abstract class AbstractEntity extends AbstractMessage implements Entity
{
    /**
     * @param string $tag
     * @return MessageRef
     */
    final public function generateMessageRef($tag = null)
    {
        return new MessageRef(static::schema()->getCurie(), $this->getEntityId(), $tag);
    }

    /**
     * @return bool
     */
    public function hasEntityId()
    {
        return $this->has(Entity::ENTITY_ID_FIELD_NAME);
    }

    /**
     * @return Identifier
     */
    public function getEntityId()
    {
        return $this->get(Entity::ENTITY_ID_FIELD_NAME);
    }

    /**
     * @return bool
     */
    final public function hasEtag()
    {
        return $this->has(Entity::ETAG_FIELD_NAME);
    }

    /**
     * @return string
     */
    final public function getEtag()
    {
        return $this->get(Entity::ETAG_FIELD_NAME);
    }

    /**
     * @param string $etag
     * @return static
     */
    final public function setEtag($etag)
    {
        return $this->setSingleValue(Entity::ETAG_FIELD_NAME, $etag);
    }

    /**
     * @return static
     */
    final public function clearEtag()
    {
        return $this->clear(Entity::ETAG_FIELD_NAME);
    }

    /**
     * @return Microtime
     */
    final public function getCreatedAt()
    {
        return $this->get(Entity::CREATED_AT_FIELD_NAME);
    }

    /**
     * @param Microtime $createdAt
     * @return static
     */
    final public function setCreatedAt(Microtime $createdAt)
    {
        return $this->setSingleValue(Entity::CREATED_AT_FIELD_NAME, $createdAt);
    }

    /**
     * @return bool
     */
    final public function hasUpdatedAt()
    {
        return $this->has(Entity::UPDATED_AT_FIELD_NAME);
    }

    /**
     * @return Microtime
     */
    final public function getUpdatedAt()
    {
        return $this->get(Entity::UPDATED_AT_FIELD_NAME);
    }

    /**
     * @param Microtime $updatedAt
     * @return static
     */
    final public function setUpdatedAt(Microtime $updatedAt)
    {
        return $this->setSingleValue(Entity::UPDATED_AT_FIELD_NAME, $updatedAt);
    }

    /**
     * @return static
     */
    final public function clearUpdatedAt()
    {
        return $this->clear(Entity::UPDATED_AT_FIELD_NAME);
    }
}
