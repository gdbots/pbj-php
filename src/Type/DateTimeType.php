<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Common\Util\DateUtils;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Field;

final class DateTimeType extends AbstractType
{
    /** @var \DateTimeZone */
    private $utc;

    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        /** @var \DateTimeInterface $value */
        Assertion::isInstanceOf($value, \DateTimeInterface::class, null, $field->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function encode($value, Field $field, Codec $codec = null)
    {
        if ($value instanceof \DateTimeInterface) {
            return $this->convertToUtc($value)->format(DateUtils::ISO8601_ZULU);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field, Codec $codec = null)
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $this->convertToUtc($value);
        }

        $date = \DateTimeImmutable::createFromFormat(DateUtils::ISO8601_ZULU, str_replace('+00:00', 'Z', $value));
        if ($date instanceof \DateTimeInterface) {
            return $this->convertToUtc($date);
        }

        throw new DecodeValueFailed(
            $value,
            $field,
            sprintf(
                'Format must be [%s].  Errors: [%s]',
                DateUtils::ISO8601_ZULU,
                // this is mutant
                print_r(\DateTimeImmutable::getLastErrors() ?: \DateTime::getLastErrors(), true)
            )
        );
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
    public function isString()
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

    /**
     * @param \DateTimeInterface $date
     *
     * @return \DateTimeInterface
     */
    private function convertToUtc(\DateTimeInterface $date)
    {
        if ($date->getOffset() !== 0) {
            if (null === $this->utc) {
                $this->utc = new \DateTimeZone('UTC');
            }

            if ($date instanceof \DateTimeImmutable) {
                $date = \DateTime::createFromFormat(
                    DateUtils::ISO8601_ZULU,
                    $date->format(DateUtils::ISO8601_ZULU)
                );
            }

            $date->setTimezone($this->utc);
            $date = \DateTimeImmutable::createFromMutable($date);
        }

        return $date;
    }
}
