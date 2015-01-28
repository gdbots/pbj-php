<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Common\Util\DateUtils;
use Gdbots\Common\Util\StringUtils;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Field;

// todo: use DateTimeImmutable?
final class DateTimeType extends AbstractType
{
    /** @var \DateTimeZone */
    private $utc;

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
            return $this->convertToUtc($value)->format(DateUtils::ISO8601);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field)
    {
        if ($value instanceof \DateTime) {
            return $this->convertToUtc($value);
        }

        if (empty($value)) {
            return null;
        }

        $date = \DateTime::createFromFormat(DateUtils::ISO8601, $value);
        if ($date instanceof \DateTime) {
            return $this->convertToUtc($date);
        }

        throw new DecodeValueFailed(
            $value,
            $field,
            sprintf(
                'Format must be [%s].  Errors: [%s]',
                DateUtils::ISO8601,
                // this is mutant
                print_r(\DateTime::getLastErrors(), true)
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
     * @param \DateTime $date
     * @return \DateTime
     */
    private function convertToUtc(\DateTime $date)
    {
        if (null === $this->utc) {
            $this->utc = new \DateTimeZone('UTC');
        }

        if ($date->getOffset() !== 0) {
            $date->setTimezone($this->utc);
        }

        return $date;
    }
}
