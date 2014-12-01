<?php

namespace Gdbots\Messages\Type;

use Assert\Assertion;
use Gdbots\Common\AbstractEnum;
use Gdbots\Messages\Field;

final class IntEnumType extends AbstractType
{
    /**
     * @see Type::guard
     */
    public function guard($value, Field $field)
    {
        /** @var AbstractEnum $value */
        Assertion::isInstanceOf($value, $field->getClassName(), null, $field->getName());
        Assertion::integer($value->getValue(), null, $field->getName());
    }

    /**
     * @see Type::encode
     */
    public function encode($value, Field $field)
    {
        if ($value instanceof AbstractEnum) {
            return (int) $value->getValue();
        }
        return 0;
    }

    /**
     * @see Type::decode
     */
    public function decode($value, Field $field)
    {
        /** @var AbstractEnum $className */
        $className = $field->getClassName();
        if (null === $value && $field->hasDefault()) {
            return $field->getDefault();
        }
        return $className::create((int) $value);
    }

    /**
     * @see Type::isNumeric
     */
    public function isNumeric()
    {
        return true;
    }
}
