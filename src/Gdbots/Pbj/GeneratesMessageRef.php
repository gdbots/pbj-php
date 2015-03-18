<?php

namespace Gdbots\Pbj;

use Gdbots\Identifiers\UuidIdentifier;

interface GeneratesMessageRef
{
    /**
     * Generates a MessageRef with an optional tag to qualify the reference.
     *
     * @param string $tag
     * @return MessageRef
     */
    public function generateMessageRef($tag = null);

    /**
     * @return UuidIdentifier
     */
    public function getMessageId();
}
