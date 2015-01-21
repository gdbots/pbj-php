<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Field;

final class DateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        /** @var \DateTime $value */
        Assertion::isInstanceOf($value, 'DateTime', null, $field->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function encode($value, Field $field)
    {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d');
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field)
    {
        if ($value instanceof \DateTime) {
            return $value;
        }
        return \DateTime::createFromFormat('!Y-m-d', $value) ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function decodesToScalar()
    {
        return false;
    }
}
