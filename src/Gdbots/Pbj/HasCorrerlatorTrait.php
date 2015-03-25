<?php

namespace Gdbots\Pbj;

/**
 * @method bool has(string $fieldName)
 * @method mixed get(string $fieldName)
 * @method static setSingleValue(string $fieldName, mixed $value)
 * @method bool isFrozen()
 */
trait HasCorrerlatorTrait
{
    /**
     * @param HasCorrelator $other
     * @return static
     */
    public function copyCorrelator(HasCorrelator $other)
    {
        if ($this->isFrozen() || !$other->hasCorrelator()) {
            return $this;
        }
        return $this->setCorrelator($other->getCorrelator());
    }

    /**
     * @return bool
     */
    public function hasCorrelator()
    {
        return $this->has('correlator');
    }

    /**
     * @return MessageRef
     */
    public function getCorrelator()
    {
        return $this->get('correlator');
    }

    /**
     * @param MessageRef $correlator
     * @return static
     */
    public function setCorrelator(MessageRef $correlator)
    {
        return $this->setSingleValue('correlator', $correlator);
    }
}
