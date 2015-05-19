<?php

namespace Gdbots\Pbj;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\Identifier;

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
     * @return bool
     */
    public function hasEntityId();

    /**
     * @return Identifier
     */
    public function getEntityId();

    /**
     * @param Identifier $entityId
     * @return static
     */
    public function setEntityId(Identifier $entityId);

    /**
     * @return static
     */
    public function clearEntityId();

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
     * @return bool
     */
    public function hasCreatedAt();

    /**
     * @return Microtime
     */
    public function getCreatedAt();

    /**
     * @param Microtime $createdAt
     * @return static
     */
    public function setCreatedAt(Microtime $createdAt);

    /**
     * @return static
     */
    public function clearCreatedAt();

    /**
     * @return bool
     */
    public function hasUpdatedAt();

    /**
     * @return Microtime
     */
    public function getUpdatedAt();

    /**
     * @param Microtime $updatedAt
     * @return static
     */
    public function setUpdatedAt(Microtime $updatedAt);

    /**
     * @return static
     */
    public function clearUpdatedAt();
}
