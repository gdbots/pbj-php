<?php

namespace Gdbots\Messages\Type;

use Assert\Assertion;
use Gdbots\Common\Enum;
use Gdbots\Messages\Field;

final class IntEnumType extends AbstractType
{
    /**
     * @see Type::guard
     */
    public function guard($value, Field $field)
    {
        /** @var Enum $value */
        Assertion::isInstanceOf($value, $field->getClassName(), null, $field->getName());
        \Assert\that($value->getValue(), null, $field->getName())
            ->integer()
            ->range(0, 4294967295);
    }

    /**
     * @see Type::encode
     */
    public function encode($value, Field $field)
    {
        if ($value instanceof Enum) {
            return (int) $value->getValue();
        }
        return 0;
    }

    /**
     * @see Type::decode
     */
    public function decode($value, Field $field)
    {
        /** @var Enum $className */
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
