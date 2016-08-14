<?php

namespace Gdbots\Pbj\WellKnown;

use Gdbots\Common\FromArray;
use Gdbots\Common\ToArray;
use Gdbots\Pbj\Enum\DynamicFieldKind;
use Gdbots\Pbj\Enum\FieldRule;
use Gdbots\Pbj\Exception\InvalidArgumentException;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Type\BooleanType;
use Gdbots\Pbj\Type\DateType;
use Gdbots\Pbj\Type\FloatType;
use Gdbots\Pbj\Type\IntType;
use Gdbots\Pbj\Type\StringType;
use Gdbots\Pbj\Type\TextType;

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
     * Fields are only used to allow for type guarding/encoding/decoding.
     *
     * @var Field[]
     */
    private static $fields;

    /** @var string */
    private $name;

    /** @var string */
    private $kind;

    /** @var mixed */
    private $value;

    /**
     * @param string $name
     * @param DynamicFieldKind $kind
     * @param mixed $value
     */
    private function __construct($name, DynamicFieldKind $kind, $value)
    {
        $this->name = $name;
        $this->kind = $kind->getValue();
        $field = self::createField($this->kind);

        $this->value = $field->getType()->decode($value, $field);
        $field->guardValue($this->value);
    }

    /**
     * @param string $kind
     *
     * @return Field
     */
    private static function createField($kind)
    {
        if (!isset(self::$fields[$kind])) {
            switch ($kind) {
                case DynamicFieldKind::STRING_VAL:
                    $type = StringType::create();
                    break;

                case DynamicFieldKind::TEXT_VAL:
                    $type = TextType::create();
                    break;

                case DynamicFieldKind::INT_VAL:
                    $type = IntType::create();
                    break;

                case DynamicFieldKind::BOOL_VAL:
                    $type = BooleanType::create();
                    break;

                case DynamicFieldKind::FLOAT_VAL:
                    $type = FloatType::create();
                    break;

                case DynamicFieldKind::DATE_VAL:
                    $type = DateType::create();
                    break;

                default:
                    throw new InvalidArgumentException(sprintf('DynamicField "%s" is not a valid type.', $kind));
            }

            self::$fields[$kind] = new Field($kind, $type, FieldRule::A_SINGLE_VALUE(), true);
        }

        return self::$fields[$kind];
    }

    /**
     * @param string $name
     * @param bool $value
     *
     * @return self
     */
    public static function createBoolVal($name, $value = false)
    {
        return new self($name, DynamicFieldKind::BOOL_VAL(), $value);
    }

    /**
     * @param string $name
     * @param \DateTime $value
     *
     * @return self
     */
    public static function createDateVal($name, \DateTime $value)
    {
        return new self($name, DynamicFieldKind::DATE_VAL(), $value);
    }

    /**
     * @param string $name
     * @param float $value
     *
     * @return self
     */
    public static function createFloatVal($name, $value = 0.0)
    {
        return new self($name, DynamicFieldKind::FLOAT_VAL(), $value);
    }

    /**
     * @param string $name
     * @param int $value
     *
     * @return self
     */
    public static function createIntVal($name, $value = 0)
    {
        return new self($name, DynamicFieldKind::INT_VAL(), $value);
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return self
     */
    public static function createStringVal($name, $value)
    {
        return new self($name, DynamicFieldKind::STRING_VAL(), $value);
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return self
     */
    public static function createTextVal($name, $value)
    {
        return new self($name, DynamicFieldKind::TEXT_VAL(), $value);
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
        $kind = key($data);

        try {
            $kind = DynamicFieldKind::create($kind);
        } catch (\Exception $e) {
            throw new InvalidArgumentException(sprintf('DynamicField "%s" is not a valid kind.', $kind));
        }

        return new self($name, $kind, $data[$kind->getValue()]);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $field = self::createField($this->kind);
        return ['name' => $this->name, $this->kind => $field->getType()->encode($this->value, $field)];
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
     * @return string
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * @return Field
     */
    public function getField()
    {
        return self::createField($this->kind);
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
            && $this->kind === $other->kind
            && $this->value === $other->value;
    }
}
