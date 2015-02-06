<?php

namespace Gdbots\Pbj\Extension;

use Gdbots\Pbj\AbstractMessage;

abstract class AbstractValueObject extends AbstractMessage implements ValueObject
{
    /**
     * {@inheritdoc}
     * This could probably use some work.  :)  low level serialization string match.
     */
    public function equals(ValueObject $other)
    {
        return json_encode($this) === json_encode($other);
    }
}
