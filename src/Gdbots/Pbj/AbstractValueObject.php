<?php

namespace Gdbots\Pbj;

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
