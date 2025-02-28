<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Exception;

use Assert\InvalidArgumentException;

final class AssertionFailed extends InvalidArgumentException implements GdbotsPbjException
{
    public function __construct($message, $code, ?string $propertyPath = null, $value = null, array $constraints = [])
    {
        if (null !== $propertyPath) {
            $message = $propertyPath . ' :: ' . $message;
        }
        parent::__construct($message, $code, $propertyPath, $value, $constraints);
    }
}
