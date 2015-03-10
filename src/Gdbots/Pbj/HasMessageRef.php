<?php

namespace Gdbots\Pbj;

use Gdbots\Identifiers\UuidIdentifier;

interface HasMessageRef
{
    /**
     * @return MessageRef
     */
    public function getMessageRef();

    /**
     * @return UuidIdentifier
     */
    public function getMessageId();
}
