<?php
declare(strict_types=1);

namespace Gdbots\Pbj;

use Gdbots\Pbj\Enum\FieldRule;
use Gdbots\Pbj\Enum\Format;
use Gdbots\Pbj\Enum\TypeName;
use Gdbots\Pbj\Type\Type;
use Gdbots\Pbj\Util\NumberUtil;
use Gdbots\Pbj\WellKnown\Identifier;

final class Field implements \JsonSerializable
{
    /**
     * Regular expression pattern for matching a valid field name.  The pattern allows
     * for camelCase fields name but snake_case is recommend.
     *
     * @constant string
     */
    const VALID_NAME_PATTERN = '/^[a-zA-Z_]{1}[a-zA-Z0-9_]*$/';

    private string $name;
    private Type $type;
    private FieldRule $rule;
    private bool $required;
    private ?int $minLength = null;
    private ?int $maxLength = null;

    /**
     * A regular expression to match against for string types.
     * @link http://spacetelescope.github.io/understanding-json-schema/reference/string.html#pattern
     *
     * @var string|null
     */
    private ?string $pattern = null;
    private ?Format $format = null;
    private ?int $min = null;
    private ?int $max = null;
    private int $precision = 10;
    private int $scale = 2;
    private mixed $default;
    private bool $useTypeDefault;
    private ?string $className;
    private ?array $anyOfCuries;
    private ?\Closure $assertion;
    private bool $overridable;

    public function __construct(
        string    $name,
        Type      $type,
        FieldRule $rule,
        bool      $required = false,
        ?int      $minLength = null,
        ?int      $maxLength = null,
        ?string   $pattern = null,
        ?Format   $format = null,
        ?int      $min = null,
        ?int      $max = null,
        int       $precision = 10,
        int       $scale = 2,
        mixed     $default = null,
        bool      $useTypeDefault = true,
        ?string   $className = null,
        ?array    $anyOfCuries = null,
        ?\Closure $assertion = null,
        bool      $overridable = false
    ) {
        Assertion::betweenLength($name, 1, 127);
        Assertion::regex($name, self::VALID_NAME_PATTERN, 'Field name must match pattern.', $name);

        if (!$type->isMessage()) {
            // anyOf is only supported on nested messages
            Assertion::nullOrClassExists($className);
            $anyOfCuries = null;
        }

        $this->name = $name;
        $this->type = $type;
        $this->rule = $rule;
        $this->required = $required;
        $this->useTypeDefault = $useTypeDefault;
        $this->className = $className;
        $this->anyOfCuries = $anyOfCuries;
        $this->assertion = $assertion;
        $this->overridable = $overridable;

        $this->applyFieldRule();
        $this->applyStringOptions($minLength, $maxLength, $pattern, $format);
        $this->applyNumericOptions($min, $max, $precision, $scale);
        $this->applyDefault($default);
    }

    private function applyFieldRule(): void
    {
        if ($this->isASet()) {
            Assertion::true(
                $this->type->allowedInSet(),
                'Field cannot be used in a set.',
                $this->name
            );
        }
    }

    private function applyStringOptions(
        ?int    $minLength = null,
        ?int    $maxLength = null,
        ?string $pattern = null,
        ?Format $format = null
    ): void {
        $minLength = (int)$minLength;
        $maxLength = (int)$maxLength;
        if ($maxLength > 0) {
            $this->maxLength = $maxLength;
            $this->minLength = NumberUtil::bound($minLength, 0, $this->maxLength);
        } else {
            // arbitrary string minimum range
            $this->minLength = NumberUtil::bound($minLength, 0, $this->type->getMaxBytes());
        }

        if (null !== $pattern) {
            $this->pattern = '/' . trim($pattern, '/') . '/';
        }

        $this->format = $format;
    }

    private function applyNumericOptions(?int $min = null, ?int $max = null, int $precision = 10, int $scale = 2): void
    {
        if (null !== $max) {
            $this->max = $max;
        }

        if (null !== $min) {
            $this->min = $min;
            if (null !== $this->max) {
                if ($this->min > $this->max) {
                    $this->min = $this->max;
                }
            }
        }

        $this->precision = NumberUtil::bound($precision, 1, 65);
        $this->scale = NumberUtil::bound($scale, 0, $this->precision);
    }

    private function applyDefault($default = null): void
    {
        $this->default = $default;

        if ($this->type->isScalar()) {
            if ($this->type->getTypeName() !== TypeName::TIMESTAMP) {
                $this->useTypeDefault = true;
            }
        } else {
            $decodeDefault = null !== $this->default && !$this->default instanceof \Closure;
            switch ($this->type->getTypeName()) {
                case TypeName::IDENTIFIER:
                    Assertion::notNull($this->className, 'Field requires a className.', $this->name);
                    if ($decodeDefault && !$this->default instanceof Identifier) {
                        $this->default = $this->type->decode($this->default, $this);
                    }
                    break;

                case TypeName::INT_ENUM:
                case TypeName::STRING_ENUM:
                    Assertion::notNull($this->className, 'Field requires a className.', $this->name);
                    if ($decodeDefault && !$this->default instanceof \BackedEnum) {
                        $this->default = $this->type->decode($this->default, $this);
                    }
                    break;

                default:
                    break;
            }
        }

        if (null !== $this->default && !$this->default instanceof \Closure) {
            $this->guardDefault($this->default);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getRule(): FieldRule
    {
        return $this->rule;
    }

    public function isASingleValue(): bool
    {
        return FieldRule::A_SINGLE_VALUE === $this->rule;
    }

    public function isASet(): bool
    {
        return FieldRule::A_SET === $this->rule;
    }

    public function isAList(): bool
    {
        return FieldRule::A_LIST === $this->rule;
    }

    public function isAMap(): bool
    {
        return FieldRule::A_MAP === $this->rule;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getMinLength(): int
    {
        return $this->minLength;
    }

    public function getMaxLength(): int
    {
        if (null === $this->maxLength) {
            return $this->type->getMaxBytes();
        }

        return $this->maxLength;
    }

    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    public function hasFormat(): bool
    {
        return null !== $this->format;
    }

    public function getFormat(): ?Format
    {
        return $this->format;
    }

    public function getMin(): int
    {
        if (null === $this->min) {
            return $this->type->getMin();
        }

        return $this->min;
    }

    public function getMax(): int
    {
        if (null === $this->max) {
            return $this->type->getMax();
        }

        return $this->max;
    }

    public function getPrecision(): int
    {
        return $this->precision;
    }

    public function getScale(): int
    {
        return $this->scale;
    }

    public function getDefault(?Message $message = null): mixed
    {
        if (null === $this->default) {
            if ($this->useTypeDefault) {
                return $this->isASingleValue() ? $this->type->getDefault() : [];
            }
            return $this->isASingleValue() ? null : [];
        }

        if ($this->default instanceof \Closure) {
            $default = call_user_func($this->default, $message, $this);
            $this->guardDefault($default);
            if (null === $default) {
                if ($this->useTypeDefault) {
                    return $this->isASingleValue() ? $this->type->getDefault() : [];
                }
                return $this->isASingleValue() ? null : [];
            }
            return $default;
        }

        return $this->default;
    }

    private function guardDefault(mixed $default): void
    {
        if ($this->isASingleValue()) {
            $this->guardValue($default);
            return;
        }

        Assertion::nullOrIsArray($default, 'Field default must be an array.', $this->name);
        if (null === $default) {
            return;
        }

        if ($this->isAMap()) {
            Assertion::true(
                !array_is_list($default),
                'Field default must be an associative array.',
                $this->name
            );
        }

        foreach ($default as $k => $v) {
            Assertion::notNull($v, 'Field default for key cannot be null.', $this->name);
            $this->guardValue($v);
        }
    }

    public function hasClassName(): bool
    {
        return null !== $this->className;
    }

    public function getClassName(): ?string
    {
        return $this->className;
    }

    public function hasAnyOfCuries(): bool
    {
        return !empty($this->anyOfCuries);
    }

    public function getAnyOfCuries(): ?array
    {
        return $this->anyOfCuries;
    }

    public function isOverridable(): bool
    {
        return $this->overridable;
    }

    public function guardValue(mixed $value): void
    {
        if ($this->required) {
            Assertion::notNull($value, 'Field is required and cannot be null.');
        }

        if (null !== $value) {
            $this->type->guard($value, $this);
        }

        if (null !== $this->assertion) {
            call_user_func($this->assertion, $value, $this);
        }
    }

    public function toArray(): array
    {
        return [
            'name'             => $this->name,
            'type'             => $this->type->getTypeValue(),
            'rule'             => $this->rule->name,
            'required'         => $this->required,
            'min_length'       => $this->minLength,
            'max_length'       => $this->maxLength,
            'pattern'          => $this->pattern,
            'format'           => $this->format ? $this->format->value : Format::UNKNOWN->value,
            'min'              => $this->min,
            'max'              => $this->max,
            'precision'        => $this->precision,
            'scale'            => $this->scale,
            'default'          => $this->getDefault(),
            'use_type_default' => $this->useTypeDefault,
            'class_name'       => $this->className,
            'any_of_curies'    => $this->anyOfCuries,
            'has_assertion'    => null !== $this->assertion,
            'overridable'      => $this->overridable,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function isCompatibleForMerge(Field $other): bool
    {
        if ($this->name !== $other->name) {
            return false;
        }

        if ($this->type !== $other->type) {
            return false;
        }

        if ($this->rule !== $other->rule) {
            return false;
        }

        if ($this->className !== $other->className) {
            return false;
        }

        if (!array_intersect($this->anyOfCuries, $other->anyOfCuries)) {
            return false;
        }

        return true;
    }

    public function isCompatibleForOverride(Field $other): bool
    {
        if (!$this->overridable) {
            return false;
        }

        if ($this->name !== $other->name) {
            return false;
        }

        if ($this->type !== $other->type) {
            return false;
        }

        if ($this->rule !== $other->rule) {
            return false;
        }

        if ($this->required !== $other->required) {
            return false;
        }

        return true;
    }
}
