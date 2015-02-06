<?php

namespace Gdbots\Pbj\Extension;

use Gdbots\Pbj\Message;

interface ValueObject extends Message
{
    /**
     * Returns true if the data of the message matches.
     *
     * @param ValueObject $other
     * @return bool
     */
    public function equals(ValueObject $other);
}
