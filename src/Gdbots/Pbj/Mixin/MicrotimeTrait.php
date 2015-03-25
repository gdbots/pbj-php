<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Common\Microtime;

/**
 * @method mixed get(string $fieldName)
 * @method static setSingleValue(string $fieldName, mixed $value)
 */
trait MicrotimeTrait
{
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
}
