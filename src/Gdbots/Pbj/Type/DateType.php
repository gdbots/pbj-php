<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Common\Util\StringUtils;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Exception\DecodeValueFailedException;
use Gdbots\Pbj\Field;

// todo: use DateTimeImmutable?
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

        if (empty($value)) {
            return null;
        }

        $date = \DateTime::createFromFormat('!Y-m-d', $value);
        if ($date instanceof \DateTime) {
            return $date;
        }

        throw new DecodeValueFailedException(
            $value,
            $this,
            $field,
            sprintf(
                'Failed to decode [%s] for field [%s] to a [%s].  Format must be [Y-m-d].  Errors: [%s]',
                is_scalar($value) ? $value : StringUtils::varToString($value),
                $field->getName(),
                $this->getTypeName()->getValue(),
                // this is mutant
                print_r(\DateTime::getLastErrors(), true)
            )
        );
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
    public function isString()
    {
        return true;
    }
}
