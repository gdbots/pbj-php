<?php

namespace Gdbots\Pbj\WellKnown;

use Gdbots\Common\FromArray;
use Gdbots\Common\ToArray;
use Gdbots\Pbj\Enum\FieldRule;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Type\IntType;
use Gdbots\Pbj\Type\Type;

/**
 * DynamicField is a wrapper for fields which would not be ideal as a map because
 * you don't know what the field name is going to be until runtime or the number
 * of fields you'll end up having will be too large.
 *
 * A common use case is a polling or custom form service.  Eventually the number of
 * fields you have is in the thousands and systems like SQL, ElasticSearch will not
 * do well with that many fields.  DynamicField is designed to be a "named union".
 *
 * For example:
 *  [
 *      // the name of the field
 *      'name' => 'your-field-name',
 *      // only one of the following values can be populated.
 *      'bool_value' => true,
 *      'float_value' => 1.0,
 *      'int_value' => 1,
 *      'string_value' => 'string',
 *      'text_value' => 'some text',
 *  ]
 */
final class DynamicField implements FromArray, ToArray, \JsonSerializable
{
    /** @var Field */
    private $field;

    /** @var mixed */
    private $value;

    /**
     * @param Field $field
     * @param $value
     */
    private function __construct(Field $field, $value)
    {
        $this->field = $field;
        $this->value = $value;
        $field->guardValue($value);
    }

    /**
     * @param string $name
     * @param int $value
     * @return self
     */
    public static function createInt($name, $value = 0)
    {
        return new self(new Field($name, IntType::create(), FieldRule::A_SINGLE_VALUE(), true), $value);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromArray(array $data = [])
    {

    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'name' => $this->field->getName(),
            $this->field->getType()->getTypeName() . '_value' => $this->value
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return string
     */
    public function toString()
    {
        return json_encode($this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
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
    public function getName()
    {
        return $this->field->getName();
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->field->getType();
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param DynamicField $other
     * @return bool
     */
    public function equals(DynamicField $other)
    {
        return $this->field->getName() === $other->field->getName()
            && $this->field->getType() === $other->field->getType()
            && $this->value === $other->value;
    }
}
