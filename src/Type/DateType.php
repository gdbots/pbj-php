<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Field;

// todo: use DateTimeImmutable?
final class DateType extends AbstractType
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
    public function encode($value, Field $field, Codec $codec = null)
    {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d');
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

        if ($value instanceof \DateTime) {
            // ensures we're always in UTC and have no time parts.
            $value = $value->format('Y-m-d');
        }

        $date = \DateTime::createFromFormat('!Y-m-d', $value, $this->getUtcTimeZone());
        if ($date instanceof \DateTime) {
            return $date;
        }

        throw new DecodeValueFailed(
            $value,
            $field,
            sprintf('Format must be [Y-m-d].  Errors: [%s]', print_r(\DateTime::getLastErrors(), true))
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
     * @return \DateTimeZone
     */
    private function getUtcTimeZone()
    {
        if (null === $this->utc) {
            $this->utc = new \DateTimeZone('UTC');
        }

        return $this->utc;
    }
}
