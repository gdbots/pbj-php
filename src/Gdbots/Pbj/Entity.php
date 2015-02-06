<?php

namespace Gdbots\Pbj;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;

interface Entity extends Message
{
    /**
     * @return Field
     */
    public static function defineEntityIdField();

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
