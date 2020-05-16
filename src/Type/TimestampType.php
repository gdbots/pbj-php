<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Common\Util\DateUtils;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;

final class TimestampType extends AbstractType
{
    public function guard($value, Field $field): void
    {
        Assertion::integer($value, null, $field->getName());
        Assertion::true(
            DateUtils::isValidTimestamp($value),
            sprintf('Field [%s] value [%d] is not a valid unix timestamp.', $field->getName(), $value),
            $field->getName()
        );
    }

    public function encode($value, Field $field, ?Codec $codec = null)
    {
        return (int)$value;
    }

    public function decode($value, Field $field, ?Codec $codec = null)
    {
        return (int)$value;
    }

    public function getDefault()
    {
        return time();
    }

    public function isNumeric(): bool
    {
        return true;
    }
}
