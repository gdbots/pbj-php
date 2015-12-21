<?php

namespace Gdbots\Pbj;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\Identifier;

interface DomainEntity extends Message
{
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
