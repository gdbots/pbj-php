<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Common\Microtime;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Field;

final class MicrotimeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        /** @var Microtime $value */
        Assertion::isInstanceOf($value, 'Gdbots\Common\Microtime', null, $field->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function encode($value, Field $field)
    {
        if ($value instanceof Microtime) {
            return $value->toString();
        }
        return '0';
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field)
    {
        if ($value instanceof Microtime) {
            return $value;
        }
        return Microtime::fromString((string) $value);
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
    public function getDefault()
    {
        return Microtime::create();
    }

    /**
     * {@inheritdoc}
     */
    public function isNumeric()
    {
        return true;
    }
}
