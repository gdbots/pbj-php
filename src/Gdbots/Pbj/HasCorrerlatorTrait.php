<?php

namespace Gdbots\Pbj;

/**
 * @method bool has(string $fieldName)
 * @method mixed get(string $fieldName)
 * @method static setSingleValue(string $fieldName, mixed $value)
 * @method static clear(string $fieldName)
 * @method bool isFrozen()
 */
trait HasCorrerlatorTrait
{
    /**
     * @param HasCorrelator $other
     * @param string $tag
     * @return static
     */
    public function correlateWith(HasCorrelator $other, $tag = null)
    {
        if ($this->isFrozen()) {
            return $this;
        }

        if (!$other->hasCorrelator()) {
            if ($other instanceof GeneratesMessageRef) {
                return $this->setCorrelator($other->generateMessageRef($tag));
            }
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
