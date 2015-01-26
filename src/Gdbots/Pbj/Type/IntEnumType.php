<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Common\Enum;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Exception\DecodeValueFailedException;
use Gdbots\Pbj\Field;

final class IntEnumType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        /** @var Enum $value */
        Assertion::isInstanceOf($value, $field->getClassName(), null, $field->getName());
        Assertion::integer($value->getValue(), null, $field->getName());
        Assertion::range($value->getValue(), $this->getMin(), $this->getMax(), null, $field->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function encode($value, Field $field)
    {
        if ($value instanceof Enum) {
            return (int) $value->getValue();
        }
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field)
    {
        /** @var Enum $className */
        $className = $field->getClassName();
        if (null === $value) {
            return null;
        }

        try {
            return $className::create((int) $value);
        } catch (\Exception $e) {
            throw new DecodeValueFailedException(
                $value,
                $this,
                $field,
                sprintf(
                    'Failed to decode value for field [%s] to an [%s].  %s',
                    $field->getName(),
                    $this->getTypeName()->getValue(),
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function decodesToScalar()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isNumeric()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getMin()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getMax()
    {
        return 65535;
    }
}
