<?php

namespace Gdbots\Pbj\Extension;

use Gdbots\Pbj\Message;

/*
 * WIP, not actually a true value object as it still has mutators
 * even though it can be frozen.
 */
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
