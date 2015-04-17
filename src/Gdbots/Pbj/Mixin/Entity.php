<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\Message;
use Gdbots\Pbj\MessageRef;

interface Entity extends Message
{
    const ENTITY_ID_FIELD_NAME = '_id';
    const ETAG_FIELD_NAME = 'etag';
    const CREATED_AT_FIELD_NAME = 'created_at';
    const UPDATED_AT_FIELD_NAME = 'updated_at';

    /**
     * @param string $tag
     * @return MessageRef
     */
    public function generateMessageRef($tag = null);

    /**
     * @return UuidIdentifier
     */
    public function generateEntityId();

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
     * @return static
     */
    public function clearEtag();

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
