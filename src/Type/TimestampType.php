<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Common\Util\DateUtils;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;

final class TimestampType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        Assertion::integer($value, null, $field->getName());
        Assertion::true(
            DateUtils::isValidTimestamp($value),
            sprintf('Field [%s] value [%d] is not a valid unix timestamp.', $field->getName(), $value),
            $field->getName()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function encode($value, Field $field, Codec $codec = null)
    {
        return (int) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field, Codec $codec = null)
    {
        return (int) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault()
    {
        return time();
    }

    /**
     * {@inheritdoc}
     */
    public function isNumeric()
    {
        return true;
    }
}
