<?php

namespace Gdbots\Messages\Type;

use Assert\Assertion;
use Gdbots\Messages\Field;

final class DecimalType extends AbstractType
{
    /**
     * @see Type::guard
     */
    public function guard($value, Field $field)
    {
        Assertion::float($value, null, $field->getName());
    }

    /**
     * @see Type::encode
     */
    public function encode($value, Field $field)
    {
        return (float) $value;
    }

    /**
     * @see Type::decode
     */
    public function decode($value, Field $field)
    {
        return (float) $value;
    }

    /**
     * @see Type::getDefault
     */
    public function getDefault()
    {
        return 0.0;
    }

    /**
     * @see Type::isNumeric
     */
    public function isNumeric()
    {
        return true;
    }
}
