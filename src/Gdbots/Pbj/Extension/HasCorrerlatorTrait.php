<?php

namespace Gdbots\Pbj\Extension;

use Gdbots\Pbj\MessageRef;

/**
 * pbj:gdbots:pbjx:event:event-execution-failed:1-0-0
 *
 * gdbots:pbj:ext:has-correlator
 * gdbots:pbj:mixin:has-correlator
 *
 * pbj:ellen:videos:mixin:has-correlator:1-0-0
 *
 * ellen:videos:mixin:has-correlator:1-0-0
 * ellen:videos:mixin:has-correlator:1-0-0
 *
 *
 * @method bool has(string $fieldName)
 * @method mixed get(string $fieldName)
 * @method static setSingleValue(string $fieldName, mixed $value)
 */
trait HasCorrerlatorTrait
{
    /**
     * @return bool
     */
    public function hasCorrelator()
    {
        return $this->has(HasCorrelator::CORRELATOR_FIELD_NAME);
    }

    /**
     * @return MessageRef
     */
    public function getCorrelator()
    {
        return $this->get(HasCorrelator::CORRELATOR_FIELD_NAME);
    }

    /**
     * @param MessageRef $correlator
     * @return static
     */
    public function setCorrelator(MessageRef $correlator)
    {
        return $this->setSingleValue(HasCorrelator::CORRELATOR_FIELD_NAME, $correlator);
    }
}
