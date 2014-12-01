<?php

namespace Gdbots\Messages\Type;

use Assert\Assertion;
use Gdbots\Common\AbstractEnum;
use Gdbots\Messages\Field;

final class StringEnumType extends AbstractType
{
    /**
     * @see Type::guard
     */
    public function guard($value, Field $field)
    {
        /** @var AbstractEnum $value */
        Assertion::isInstanceOf($value, $field->getClassName(), null, $field->getName());
        Assertion::string($value->getValue(), null, $field->getName());
    }

    /**
     * @see Type::encode
     */
    public function encode($value, Field $field)
    {
        if ($value instanceof AbstractEnum) {
            return (string) $value->getValue();
        }
        return null;
    }

    /**
     * @see Type::decode
     */
    public function decode($value, Field $field)
    {
        /** @var AbstractEnum $className */
        $className = $field->getClassName();
        if (empty($value) && $field->hasDefault()) {
            return $field->getDefault();
        }
        return $className::create((string) $value);
    }

    /**
     * @see Type::isString
     */
    public function isString()
    {
        return true;
    }
}
