<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Common\Util\StringUtils;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Type\Type;

class DecodeValueFailed extends \InvalidArgumentException implements GdbotsPbjException
{
    /** @var mixed */
    private $value;

    /** @var Type */
    private $type;

    /** @var Field */
    private $field;

    /**
     * @param mixed $value
     * @param Type $type
     * @param string Field $field
     * @param string $message
     */
    public function __construct($value, Type $type, Field $field, $message = null)
    {
        $this->value = $value;
        $this->type = $type;
        $this->field = $field;
        if (null === $message) {
            $message = sprintf(
                'Failed to decode [%s] for field [%s] to a [%s].',
                is_scalar($this->value) ? $this->value : StringUtils::varToString($this->value),
                $this->field->getName(),
                $this->type->getTypeName()->getValue()
            );
        }
        parent::__construct($message);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->field->getName();
    }
}

