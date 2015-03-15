<?php

namespace Gdbots\Pbj;

/**
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
