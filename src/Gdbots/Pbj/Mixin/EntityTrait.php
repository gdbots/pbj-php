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
trait EntityTrait
{
    /**
     * @return bool
     */
    public function hasEntityId()
    {
        return $this->has(Entity::ENTITY_ID_FIELD_NAME);
    }

    /**
     * @return UuidIdentifier
     */
    public function getEntityId()
    {
        return $this->get(Entity::ENTITY_ID_FIELD_NAME);
    }

    /**
     * @param UuidIdentifier $id
     * @return static
     */
    public function setEntityId(UuidIdentifier $id)
    {
        return $this->setSingleValue(Entity::ENTITY_ID_FIELD_NAME, $id);
    }

    /**
     * @return bool
     */
    public function hasEtag()
    {
        return $this->has(Entity::ETAG_FIELD_NAME);
    }

    /**
     * @return string
     */
    public function getEtag()
    {
        return $this->get(Entity::ETAG_FIELD_NAME);
    }

    /**
     * @param string $etag
     * @return static
     */
    public function setEtag($etag)
    {
        return $this->setSingleValue(Entity::ETAG_FIELD_NAME, $etag);
    }

    /**
     * @return bool
     */
    public function hasCreatedAt()
    {
        return $this->has(Entity::CREATED_AT_FIELD_NAME);
    }

    /**
     * @return Microtime
     */
    public function getCreatedAt()
    {
        return $this->get(Entity::CREATED_AT_FIELD_NAME);
    }

    /**
     * @param Microtime $microtime
     * @return static
     */
    public function setCreatedAt(Microtime $microtime)
    {
        return $this->setSingleValue(Entity::CREATED_AT_FIELD_NAME, $microtime);
    }

    /**
     * @return bool
     */
    public function hasUpdatedAt()
    {
        return $this->has(Entity::UPDATED_AT_FIELD_NAME);
    }

    /**
     * @return Microtime
     */
    public function getUpdatedAt()
    {
        return $this->get(Entity::UPDATED_AT_FIELD_NAME);
    }

    /**
     * @param Microtime $microtime
     * @return static
     */
    public function setUpdatedAt(Microtime $microtime)
    {
        return $this->setSingleValue(Entity::UPDATED_AT_FIELD_NAME, $microtime);
    }
}
