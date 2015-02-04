<?php

namespace Gdbots\Pbj;

use Gdbots\Common\Microtime;
use Gdbots\Identifiers\UuidIdentifier;

interface Response extends Message
{
    /**
     * @return UuidIdentifier
     */
    public function getResponseId();

    /**
     * @param UuidIdentifier $id
     * @return static
     */
    public function setResponseId(UuidIdentifier $id);

    /**
     * @return Microtime
     */
    public function getMicrotime();

    /**
     * @param Microtime $microtime
     * @return static
     */
    public function setMicrotime(Microtime $microtime);

    /**
     * @return UuidIdentifier
     */
    public function getRequestId();

    /**
     * @param UuidIdentifier $id
     * @return static
     */
    public function setRequestId(UuidIdentifier $id);
}
