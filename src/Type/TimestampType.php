<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Util\DateUtil;

final class TimestampType extends AbstractType
{
    public function guard($value, Field $field): void
    {
        $fieldName = $field->getName();
        Assertion::integer($value, null, $fieldName);
        Assertion::true(
            DateUtil::isValidTimestamp($value),
            'Field must be a valid unix timestamp.',
            $fieldName
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
