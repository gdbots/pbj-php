<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\Message;

interface Entity extends Message
{
    const ENTITY_ID_FIELD_NAME = 'id';
    const ETAG_FIELD_NAME = 'etag';
    const CREATED_AT_FIELD_NAME = 'created_at';
    const UPDATED_AT_FIELD_NAME = 'updated_at';

    /**
     * @return UuidIdentifier
     */
    public function generateEntityId();

    /**
     * @return bool
     */
    public function hasEntityId();

    /**
     * @return UuidIdentifier
     */
    public function getEntityId();

    /**
     * @param UuidIdentifier $id
     * @return static
     */
    public function setEntityId(UuidIdentifier $id);

    /**
     * @return bool
     */
    public function hasEtag();

    /**
     * @return string
     */
    public function getEtag();

    /**
     * @param string $etag
     * @return static
     */
    public function setEtag($etag);

    /**
     * @return bool
     */
    public function hasCreatedAt();

    /**
     * @return Microtime
     */
    public function getCreatedAt();

    /**
     * @param Microtime $microtime
     * @return static
     */
    public function setCreatedAt(Microtime $microtime);

    /**
     * @return bool
     */
    public function hasUpdatedAt();

    /**
     * @return Microtime
     */
    public function getUpdatedAt();

    /**
     * @param Microtime $microtime
     * @return static
     */
    public function setUpdatedAt(Microtime $microtime);
}
