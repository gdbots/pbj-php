<?php

namespace Gdbots\Messages\Type;

use Gdbots\Messages\Assertion;
use Gdbots\Messages\Field;

final class Decimal extends AbstractType
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
        // http://stackoverflow.com/questions/9079158/php-dropping-decimals-without-rounding-up
        // http://stackoverflow.com/questions/10643273/no-truncate-function-in-php-options
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
