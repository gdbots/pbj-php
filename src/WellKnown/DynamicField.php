<?php

namespace Gdbots\Pbj\WellKnown;

use Gdbots\Common\FromArray;
use Gdbots\Common\ToArray;
use Gdbots\Pbj\Enum\DynamicFieldTypeName;
use Gdbots\Pbj\Enum\FieldRule;
use Gdbots\Pbj\Exception\InvalidArgumentException;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Type\BooleanType;
use Gdbots\Pbj\Type\DateType;
use Gdbots\Pbj\Type\FloatType;
use Gdbots\Pbj\Type\IntType;
use Gdbots\Pbj\Type\StringType;
use Gdbots\Pbj\Type\TextType;
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
 *      'bool_val' => true,
 *      'date_val' => '2015-12-25',
 *      'float_val' => 1.0,
 *      'int_val' => 1,
 *      'string_val' => 'string',
 *      'text_val' => 'some text',
 *  ]
 */
final class DynamicField implements FromArray, ToArray, \JsonSerializable
{
    /**
     * Fields are only used to allow for type guarding/decoding.
     *
     * @var Field[]
     */
    private static $fields;

    /** @var string */
    private $name;

    /** @var string */
    private $typeName;

    /** @var mixed */
    private $value;

    /**
     * @param string $name
     * @param DynamicFieldTypeName $typeName
     * @param mixed $value
     */
    private function __construct($name, DynamicFieldTypeName $typeName, $value)
    {
        $this->name = $name;
        $this->typeName = $typeName->getValue();
        $field = self::getField($this->typeName);

        $this->value = $field->getType()->decode($value, $field);
        $field->guardValue($this->value);
    }

    /**
     * @param string $typeName  A DynamicFieldTypeName value.
     *
     * @return Field
     */
    private static function getField($typeName)
    {
        if (!isset(self::$fields[$typeName])) {
            switch ($typeName) {
                case DynamicFieldTypeName::STRING_VAL:
                    $type = StringType::create();
                    break;

                case DynamicFieldTypeName::TEXT_VAL:
                    $type = TextType::create();
                    break;

                case DynamicFieldTypeName::INT_VAL:
                    $type = IntType::create();
                    break;

                case DynamicFieldTypeName::BOOL_VAL:
                    $type = BooleanType::create();
                    break;

                case DynamicFieldTypeName::FLOAT_VAL:
                    $type = FloatType::create();
                    break;

                case DynamicFieldTypeName::DATE_VAL:
                    $type = DateType::create();
                    break;

                default:
                    throw new InvalidArgumentException(sprintf('DynamicField "%s" is not a valid type.', $typeName));
            }

            self::$fields[$typeName] = new Field($typeName, $type, FieldRule::A_SINGLE_VALUE(), true);
        }

        return self::$fields[$typeName];
    }

    /**
     * @param string $name
     * @param bool $value
     *
     * @return self
     */
    public static function createBoolVal($name, $value = false)
    {
        return new self($name, DynamicFieldTypeName::BOOL_VAL(), $value);
    }

    /**
     * @param string $name
     * @param \DateTime $value
     *
     * @return self
     */
    public static function createDateVal($name, \DateTime $value)
    {
        return new self($name, DynamicFieldTypeName::DATE_VAL(), $value);
    }

    /**
     * @param string $name
     * @param float $value
     *
     * @return self
     */
    public static function createFloatVal($name, $value = 0.0)
    {
        return new self($name, DynamicFieldTypeName::FLOAT_VAL(), $value);
    }

    /**
     * @param string $name
     * @param int $value
     *
     * @return self
     */
    public static function createIntVal($name, $value = 0)
    {
        return new self($name, DynamicFieldTypeName::INT_VAL(), $value);
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return self
     */
    public static function createStringVal($name, $value)
    {
        return new self($name, DynamicFieldTypeName::STRING_VAL(), $value);
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return self
     */
    public static function createTextVal($name, $value)
    {
        return new self($name, DynamicFieldTypeName::TEXT_VAL(), $value);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromArray(array $data = [])
    {
        if (!isset($data['name'])) {
            throw new InvalidArgumentException('DynamicField "name" property must be set.');
        }

        $name = $data['name'];
        unset($data['name']);
        $typeName = key($data);

        try {
            $type = DynamicFieldTypeName::create($typeName);
        } catch (\Exception $e) {
            throw new InvalidArgumentException(sprintf('DynamicField "%s" is not a valid type.', $typeName));
        }

        return new self($name, $type, $data[$typeName]);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return ['name' => $this->name, $this->typeName => $this->value];
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return self::getField($this->typeName)->getType();
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
     *
     * @return bool
     */
    public function equals(DynamicField $other)
    {
        return $this->name === $other->name
            && self::getField($this->typeName) === self::getField($other->typeName)
            && $this->value === $other->value;
    }
}
