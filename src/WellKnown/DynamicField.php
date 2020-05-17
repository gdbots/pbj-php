<?php
declare(strict_types=1);

namespace Gdbots\Pbj\WellKnown;

use Gdbots\Pbj\Assertion;
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
final class DynamicField implements \JsonSerializable
{
    /**
     * Regular expression pattern for matching a valid dynamic field name.
     *
     * @constant string
     */
    const VALID_NAME_PATTERN = '/^[a-zA-Z_]{1}[a-zA-Z0-9_-]*$/';

    /**
     * Fields are only used to allow for type guarding/encoding/decoding.
     *
     * @var Field[]
     */
    private static array $fields;

    private string $name;
    private string $kind;
    private $value;

    private function __construct(string $name, DynamicFieldKind $kind, $value)
    {
        Assertion::betweenLength($name, 1, 127);
        Assertion::regex($name, self::VALID_NAME_PATTERN,
            sprintf('DynamicField name [%s] must match pattern [%s].', $name, self::VALID_NAME_PATTERN)
        );

        $this->name = $name;
        $this->kind = $kind->getValue();
        $field = self::createField($this->kind);

        $this->value = $field->getType()->decode($value, $field);
        $field->guardValue($this->value);
    }

    private static function createField(string $kind): Field
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

    public static function createBoolVal(string $name, bool $value = false): self
    {
        return new self($name, DynamicFieldKind::BOOL_VAL(), $value);
    }

    public static function createDateVal(string $name, \DateTimeInterface $value): self
    {
        return new self($name, DynamicFieldKind::DATE_VAL(), $value);
    }

    public static function createFloatVal(string $name, float $value = 0.0): self
    {
        return new self($name, DynamicFieldKind::FLOAT_VAL(), $value);
    }

    public static function createIntVal(string $name, int $value = 0): self
    {
        return new self($name, DynamicFieldKind::INT_VAL(), $value);
    }

    public static function createStringVal(string $name, string $value): self
    {
        return new self($name, DynamicFieldKind::STRING_VAL(), $value);
    }

    public static function createTextVal(string $name, string $value): self
    {
        return new self($name, DynamicFieldKind::TEXT_VAL(), $value);
    }

    public static function fromArray(array $data = []): self
    {
        if (!isset($data['name'])) {
            throw new InvalidArgumentException('DynamicField "name" property must be set.');
        }

        $name = $data['name'];
        unset($data['name']);
        $kind = key($data);

        try {
            $kind = DynamicFieldKind::create($kind);
        } catch (\Throwable $e) {
            throw new InvalidArgumentException(sprintf('DynamicField "%s" is not a valid kind.', $kind));
        }

        return new self($name, $kind, $data[$kind->getValue()]);
    }

    public function toArray(): array
    {
        $field = self::createField($this->kind);
        return ['name' => $this->name, $this->kind => $field->getType()->encode($this->value, $field)];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toString(): string
    {
        return json_encode($this);
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getKind(): string
    {
        return $this->kind;
    }

    public function getField(): Field
    {
        return self::createField($this->kind);
    }

    public function getValue()
    {
        return $this->value;
    }

    public function equals(DynamicField $other): bool
    {
        return $this->name === $other->name
            && $this->kind === $other->kind
            && $this->value === $other->value;
    }
}
