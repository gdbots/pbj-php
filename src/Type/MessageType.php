<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Message;

final class MessageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        /** @var Message $value */
        Assertion::isInstanceOf($value, 'Gdbots\Pbj\Message', null, $field->getName());
        Assertion::isInstanceOf($value, $field->getClassName(), null, $field->getName());

        if (!$field->hasAnyOfClassNames()) {
            return;
        }

        $classNames = $field->getAnyOfClassNames();
        if (empty($classNames)) {
            // means it can be "any message"
            return;
        }

        foreach ($classNames as $className) {
            if ($value instanceof $className) {
                return;
            }
        }

        Assertion::true(
            false,
            sprintf(
                'Field [%s] must be an instance of at least one of: %s.',
                $field->getName(),
                implode(',', $classNames)
            ),
            $field->getName()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function encode($value, Field $field, Codec $codec = null)
    {
        return $codec->encodeMessage($value, $field);
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field, Codec $codec = null)
    {
        return $codec->decodeMessage($value, $field);
    }

    /**
     * {@inheritdoc}
     */
    public function isScalar()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function encodesToScalar()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isMessage()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function allowedInSet()
    {
        return false;
    }
}
