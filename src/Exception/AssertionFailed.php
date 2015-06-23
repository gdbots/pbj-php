<?php

namespace Gdbots\Pbj\Exception;

use Assert\InvalidArgumentException;

class AssertionFailed extends InvalidArgumentException implements GdbotsPbjException
{
    /**
     * @param string $message
     * @param int $code
     * @param null $propertyPath
     * @param $value
     * @param array $constraints
     */
    public function __construct($message, $code, $propertyPath = null, $value, array $constraints = array())
    {
        if (null !== $propertyPath) {
            $message = $propertyPath . ' :: ' . $message;
        }
        parent::__construct($message, $code, $propertyPath, $value, $constraints);
    }
}
