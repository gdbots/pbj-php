<?php

namespace Gdbots\Pbj\Extension;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\Type as T;

abstract class AbstractEntity extends AbstractMessage implements Entity
{
    /**
     * @return Field
     */
    public static function defineEntityIdField()
    {
        return Fb::create(EntitySchema::ENTITY_ID_FIELD_NAME, T\UuidType::create())
            ->required()
            ->build();
    }

    /**
     * {@inheritdoc}
     */
    final public function hasEntityId()
    {
        return $this->has(EntitySchema::ENTITY_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId()
    {
        return $this->get(EntitySchema::ENTITY_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    public function setEntityId(UuidIdentifier $id)
    {
        return $this->setSingleValue(EntitySchema::ENTITY_ID_FIELD_NAME, $id);
    }

    /**
     * {@inheritdoc}
     */
    final public function hasEtag()
    {
        return $this->has(EntitySchema::ETAG_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getEtag()
    {
        return $this->get(EntitySchema::ETAG_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setEtag($etag)
    {
        return $this->setSingleValue(EntitySchema::ETAG_FIELD_NAME, $etag);
    }

    /**
     * {@inheritdoc}
     */
    final public function hasCreatedAt()
    {
        return $this->has(EntitySchema::CREATED_AT_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getCreatedAt()
    {
        return $this->get(EntitySchema::CREATED_AT_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setCreatedAt(Microtime $microtime)
    {
        return $this->setSingleValue(EntitySchema::CREATED_AT_FIELD_NAME, $microtime);
    }

    /**
     * {@inheritdoc}
     */
    final public function hasUpdatedAt()
    {
        return $this->has(EntitySchema::UPDATED_AT_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    final public function getUpdatedAt()
    {
        return $this->get(EntitySchema::UPDATED_AT_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    final public function setUpdatedAt(Microtime $microtime)
    {
        return $this->setSingleValue(EntitySchema::UPDATED_AT_FIELD_NAME, $microtime);
    }
}
