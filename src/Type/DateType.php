<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Field;

final class DateType extends AbstractType
{
    private ?\DateTimeZone $utc = null;

    public function guard($value, Field $field): void
    {
        Assertion::isInstanceOf($value, \DateTimeInterface::class, null, $field->getName());
    }

    public function encode($value, Field $field, ?Codec $codec = null)
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return null;
    }

    public function decode($value, Field $field, ?Codec $codec = null)
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            // ensures we're always in UTC and have no time parts.
            $value = $value->format('Y-m-d');
        }

        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value, $this->getUtcTimeZone());
        if ($date instanceof \DateTimeImmutable) {
            return $date;
        }

        throw new DecodeValueFailed(
            $value,
            $field,
            sprintf(
                'Format must be [Y-m-d].  Errors: [%s]',
                print_r(\DateTimeImmutable::getLastErrors(), true)
            )
        );
    }

    public function isScalar(): bool
    {
        return false;
    }

    public function isString(): bool
    {
        return true;
    }

    public function allowedInSet(): bool
    {
        return false;
    }

    private function getUtcTimeZone(): \DateTimeZone
    {
        if (null === $this->utc) {
            $this->utc = new \DateTimeZone('UTC');
        }

        return $this->utc;
    }
}
