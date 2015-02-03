<?php

namespace Gdbots\Pbj;

use Gdbots\Common\Enum;
use Gdbots\Common\ToArray;
use Gdbots\Common\Util\ArrayUtils;
use Gdbots\Common\Util\NumberUtils;
use Gdbots\Pbj\Enum\FieldRule;
use Gdbots\Pbj\Enum\Format;
use Gdbots\Pbj\Enum\TypeName;
use Gdbots\Pbj\Exception\AssertionFailed;
use Gdbots\Pbj\Type\Type;

final class Field implements ToArray, \JsonSerializable
{
    /**
     * Regular expression pattern for matching a valid field name.  The pattern allows
     * for camelCase fields name but snake_case is recommend.
     *
     * @constant string
     */
    const VALID_NAME_PATTERN = '/^[a-zA-Z_]{1}[a-zA-Z0-9_]+$/';

    /** @var string */
    private $name;

    /** @var Type */
    private $type;

    /** @var FieldRule */
    private $rule;

    /** @var bool */
    private $required = false;

    /** @var int */
    private $minLength;

    /** @var int */
    private $maxLength;

    /**
     * A regular expression to match against for string types.
     * @link http://spacetelescope.github.io/understanding-json-schema/reference/string.html#pattern
     *
     * @var string
     */
    private $pattern;

    /**
     * @link http://spacetelescope.github.io/understanding-json-schema/reference/string.html#format
     *
     * @var Format
     */
    private $format;

    /** @var int */
    private $min;

    /** @var int */
    private $max;

    /** @var int */
    private $precision = 10;

    /** @var int */
    private $scale = 2;

    /** @var mixed */
    private $default;

    /** @var bool */
    private $useTypeDefault = true;

    /** @var string */
    private $className;

    /** @var \Closure */
    private $assertion;

    /**
     * @param string $name
     * @param Type $type
     * @param FieldRule $rule
     * @param bool $required
     * @param null|int $minLength
     * @param null|int $maxLength
     * @param null|string $pattern
     * @param null|string $format
     * @param null|int $min
     * @param null|int $max
     * @param int $precision
     * @param int $scale
     * @param null|mixed $default
     * @param bool $useTypeDefault
     * @param null|string $className
     * @param callable|null $assertion
     */
    public function __construct(
        $name,
        Type $type,
        FieldRule $rule = null,
        $required = false,
        $minLength = null,
        $maxLength = null,
        $pattern = null,
        $format = null,
        $min = null,
        $max = null,
        $precision = 10,
        $scale = 2,
        $default = null,
        $useTypeDefault = true,
        $className = null,
        \Closure $assertion = null
    ) {
        Assertion::betweenLength($name, 1, 127);
        Assertion::regex($name, self::VALID_NAME_PATTERN,
            sprintf('Field [%s] must match pattern [%s].', $name, self::VALID_NAME_PATTERN)
        );
        Assertion::boolean($required);
        Assertion::boolean($useTypeDefault);
        Assertion::nullOrClassExists($className);

        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
        $this->useTypeDefault = $useTypeDefault;
        $this->className = $className;
        $this->assertion = $assertion;

        $this->applyFieldRule($rule);
        $this->applyStringOptions($minLength, $maxLength, $pattern, $format);
        $this->applyNumericOptions($min, $max, $precision, $scale);
        $this->applyDefault($default);
    }

    /**
     * @param FieldRule $rule
     * @throws AssertionFailed
     */
    private function applyFieldRule(FieldRule $rule = null)
    {
        $this->rule = $rule ?: FieldRule::A_SINGLE_VALUE();
        if ($this->isASet() || $this->isAList()) {
            Assertion::true(
                $this->type->isScalar(),
                sprintf('Field [%s] must decode as a scalar to be used in a set of list.', $this->name)
            );
        }
    }

    /**
     * @param null|int $minLength
     * @param null|int $maxLength
     * @param null|string $pattern
     * @param null|string $format
     */
    private function applyStringOptions($minLength = null, $maxLength = null, $pattern = null, $format = null)
    {
        $minLength = (int) $minLength;
        $maxLength = (int) $maxLength;
        if ($maxLength > 0) {
            $this->maxLength = $maxLength;
            $this->minLength = NumberUtils::bound($minLength, 0, $this->maxLength);
        } else {
            // arbitrary string minimum range
            $this->minLength = NumberUtils::bound($minLength, 0, $this->type->getMaxBytes());
        }

        $this->pattern = $pattern;
        if (null !== $format && in_array($format, Format::values())) {
            $this->format = Format::create($format);
        } else {
            $this->format = Format::UNKNOWN();
        }
    }

    /**
     * @param null|int $min
     * @param null|int $max
     * @param int $precision
     * @param int $scale
     */
    private function applyNumericOptions($min = null, $max = null, $precision = 10, $scale = 2)
    {
        if (null !== $max) {
            $this->max = (int) $max;
        }

        if (null !== $min) {
            $this->min = (int) $min;
            if (null !== $this->max) {
                if ($this->min > $this->max) {
                    $this->min = $this->max;
                }
            }
        }

        $this->precision = NumberUtils::bound((int) $precision, 1, 65);
        $this->scale = NumberUtils::bound((int) $scale, 0, $this->precision);
    }

    /**
     * @param mixed $default
     * @throws AssertionFailed
     * @throws \Exception
     */
    private function applyDefault($default = null)
    {
        $this->default = $default;

        if ($this->type->isScalar()) {
            $this->useTypeDefault = true;
        } else {
            switch ($this->type->getTypeName()->getValue()) {
                case TypeName::INT_ENUM:
                case TypeName::STRING_ENUM:
                    Assertion::notNull($this->className, sprintf('Field [%s] requires a className.', $this->name));
                    if (!$this->default instanceof Enum) {
                        $this->default = $this->type->decode($this->default, $this);
                    }
                    break;

                case TypeName::MESSAGE:
                    Assertion::notNull($this->className, sprintf('Field [%s] requires a className.', $this->name));
                    break;

                default:
                    break;
            }
        }

        if (null !== $this->default && !$this->default instanceof \Closure) {
            $this->guardDefault($this->default);
        }
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
        return $this->type;
    }

    /**
     * @return FieldRule
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @return bool
     */
    public function isASingleValue()
    {
        return FieldRule::A_SINGLE_VALUE === $this->rule->getValue();
    }

    /**
     * @return bool
     */
    public function isASet()
    {
        return FieldRule::A_SET === $this->rule->getValue();
    }

    /**
     * @return bool
     */
    public function isAList()
    {
        return FieldRule::A_LIST === $this->rule->getValue();
    }

    /**
     * @return bool
     */
    public function isAMap()
    {
        return FieldRule::A_MAP === $this->rule->getValue();
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @return int
     */
    public function getMinLength()
    {
        return $this->minLength;
    }

    /**
     * @return int
     */
    public function getMaxLength()
    {
        if (null === $this->maxLength) {
            return $this->type->getMaxBytes();
        }
        return $this->maxLength;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @return Format
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return int
     */
    public function getMin()
    {
        if (null === $this->min) {
            return $this->type->getMin();
        }
        return $this->min;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        if (null === $this->max) {
            return $this->type->getMax();
        }
        return $this->max;
    }

    /**
     * @return int
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @return int
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * @param Message $message
     * @return mixed
     */
    public function getDefault(Message $message = null)
    {
        if (null === $this->default) {
            if ($this->useTypeDefault) {
                return $this->isASingleValue() ? $this->type->getDefault() : [];
            }
            return $this->isASingleValue() ? null : [];
        }

        if ($this->default instanceof \Closure) {
            $default = call_user_func($this->default, $message);
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

    /**
     * @param mixed $default
     * @throws AssertionFailed
     * @throws \Exception
     */
    private function guardDefault($default)
    {
        if ($this->isASingleValue()) {
            $this->guardValue($default);
            return;
        }

        Assertion::nullOrIsArray($default, sprintf('Field [%s] default must be an array.', $this->name));
        if (null === $default) {
            return;
        }

        if ($this->isAMap()) {
            // todo: review, must a map be scalar too?
            Assertion::true(
                ArrayUtils::isAssoc($default),
                sprintf('Field [%s] default must be an associative array.', $this->name)
            );
        }

        foreach ($default as $k => $v) {
            Assertion::notNull($v, sprintf('Field [%s] default for key [%s] cannot be null.', $this->name, $k));
            $this->guardValue($v);
        }
    }

    /**
     * @return bool
     */
    public function hasClassName()
    {
        return null !== $this->className;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param mixed $value
     * @throws AssertionFailed
     * @throws \Exception
     */
    public function guardValue($value)
    {
        if ($this->required) {
            Assertion::notNull($value, sprintf('Field [%s] is required and cannot be null.', $this->name));
        }

        if (null !== $value) {
            $this->type->guard($value, $this);
        }

        if (null !== $this->assertion) {
            call_user_func($this->assertion, $value, $this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'name'          => $this->name,
            'type'          => $this->type->getTypeName()->getValue(),
            'rule'          => $this->rule->getName(),
            'required'      => $this->required,
            'min_length'    => $this->minLength,
            'max_length'    => $this->maxLength,
            'pattern'       => $this->pattern,
            'format'        => $this->format->getValue(),
            'min'           => $this->min,
            'max'           => $this->max,
            'precision'     => $this->precision,
            'scale'         => $this->scale,
            'default'       => $this->getDefault(),
            'use_type_default' => $this->useTypeDefault,
            'class_name'    => $this->className,
            'has_assertion' => null !== $this->assertion
        ];
    }

    /**
     * @return array
     */
    final public function jsonSerialize()
    {
        return $this->toArray();
    }
}