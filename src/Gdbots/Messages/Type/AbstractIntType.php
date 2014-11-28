<?php

namespace Gdbots\Messages\Type;

use Gdbots\Messages\Field;

abstract class AbstractIntType extends AbstractType
{
    /**
     * @see Type::encode
     */
    public function encode($value, Field $field)
    {
        return (int) $value;
    }

    /**
     * @see Type::decode
     */
    public function decode($value, Field $field)
    {
        return (int) $value;
    }

    /**
     * @see Type::getDefault
     */
    public function getDefault()
    {
        return 0;
    }

    /**
     * @see Type::isNumeric
     */
    public function isNumeric()
    {
        return true;
    }
}