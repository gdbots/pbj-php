<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Common\Microtime;

/**
 * @method bool has(string $fieldName)
 * @method mixed get(string $fieldName)
 * @method static setSingleValue(string $fieldName, mixed $value)
 * @method static clear(string $fieldName)
 */
trait MicrotimeTrait
{
    /**
     * @return bool
     */
    public function hasMicrotime()
    {
        return $this->has('microtime');
    }

    /**
     * @return Microtime
     */
    public function getMicrotime()
    {
        return $this->get('microtime');
    }

    /**
     * @param Microtime $microtime
     * @return static
     */
    public function setMicrotime(Microtime $microtime)
    {
        return $this->setSingleValue('microtime', $microtime);
    }

    /**
     * @return static
     */
    public function clearMicrotime()
    {
        return $this->clear('microtime');
    }
}
