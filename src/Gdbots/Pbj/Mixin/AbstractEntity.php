<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\GeneratesMessageRef;
use Gdbots\Pbj\GeneratesMessageRefTrait;

abstract class AbstractEntity extends AbstractMessage implements Entity, GeneratesMessageRef
{
    use GeneratesMessageRefTrait;

    /**
     * {@inheritdoc}
     */
    final public function getMessageId()
    {
        return $this->getEntityId();
    }

    /**
     * @return UuidIdentifier
     */
    public function generateEntityId()
    {
        return static::schema()->getField(Entity::ENTITY_ID_FIELD_NAME)->getType()->getDefault();
    }

    /**
     * @return bool
     */
    final public function hasEntityId()
    {
        return $this->has(Entity::ENTITY_ID_FIELD_NAME);
    }

    /**
     * @return UuidIdentifier
     */
    final public function getEntityId()
    {
        return $this->get(Entity::ENTITY_ID_FIELD_NAME);
    }

    /**
     * @param UuidIdentifier $id
     * @return static
     */
    final public function setEntityId(UuidIdentifier $id)
    {
        return $this->setSingleValue(Entity::ENTITY_ID_FIELD_NAME, $id);
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
     * @return bool
     */
    final public function hasCreatedAt()
    {
        return $this->has(Entity::CREATED_AT_FIELD_NAME);
    }

    /**
     * @return Microtime
     */
    final public function getCreatedAt()
    {
        return $this->get(Entity::CREATED_AT_FIELD_NAME);
    }

    /**
     * @param Microtime $microtime
     * @return static
     */
    final public function setCreatedAt(Microtime $microtime)
    {
        return $this->setSingleValue(Entity::CREATED_AT_FIELD_NAME, $microtime);
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
     * @param Microtime $microtime
     * @return static
     */
    final public function setUpdatedAt(Microtime $microtime)
    {
        return $this->setSingleValue(Entity::UPDATED_AT_FIELD_NAME, $microtime);
    }
}
