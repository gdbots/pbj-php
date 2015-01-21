<?php

namespace Gdbots\Pbj;

use Gdbots\Common\ToArray;
use Gdbots\Common\Util\ArrayUtils;
use Gdbots\Common\Util\NumberUtils;
use Gdbots\Pbj\Enum\FieldRule;
use Gdbots\Pbj\Enum\Format;
use Gdbots\Pbj\Type\IntEnum;
use Gdbots\Pbj\Type\StringEnum;
use Gdbots\Pbj\Type\Type;

final class Field implements ToArray, \JsonSerializable
{
    /** @var string */
    private $name;

    /** @var Type */
    private $type;

    /** @var FieldRule */
    private $rule;

    /** @var bool */
    private $required = false;

    /** @var int */
    private $minLength = 0;

    /** @var int */
    private $maxLength = 0;

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
    private $min = 0;

    /** @var int */
    private $max = 0;

    /** @var int */
    private $precision = 10;

    /** @var int */
    private $scale = 0;

    /** @var mixed */
    private $default;

    /** @var string */
    private $className;

    /** @var \Closure */
    private $assertion;

    /**
     * @param string $name
     * @param Type $type
     * @param FieldRule $rule
     * @param bool $required
     * @param int $minLength
     * @param int $maxLength
     * @param null|string $pattern
     * @param null|string $format
     * @param int $min
     * @param int $max
     * @param int $precision
     * @param int $scale
     * @param null|mixed $default
     * @param null|string $className
     * @param callable|null $assertion
     */
    public function __construct(
        $name,
        Type $type,
        FieldRule $rule = null,
        $required = false,
        $minLength = 0,
        $maxLength = 0,
        $pattern = null,
        $format = null,
        $min = 0,
        $max = 0,
        $precision = 10,
        $scale = 0,
        $default = null,
        $className = null,
        \Closure $assertion = null
    ) {
        Assertion::string($name);
        Assertion::boolean($required);
        Assertion::nullOrString($className);

        $this->name = $name;
        $this->type = $type;
        $this->rule = $rule ?: FieldRule::A_SINGLE_VALUE();
        $this->required = $required;

        // string constraints
        $this->minLength = (int) $minLength;
        $this->maxLength = (int) $maxLength;
        $this->pattern = $pattern;
        if (null !== $format && in_array($format, Format::values())) {
            $this->format = Format::create($format);
        } else {
            $this->format = Format::UNKNOWN();
        }

        // numeric constraints
        $this->min = (int) $min;
        $this->max = (int) $max;
        if ($this->min > $this->max) {
            $this->min = $this->max < 0 ? $this->max : 0;
        }
        $this->precision = NumberUtils::bound((int) $precision, 1, 65);
        $this->scale = NumberUtils::bound((int) $scale, 0, $this->precision);

        $this->default = $default;
        $this->className = $className;
        $this->assertion = $assertion;

        if ($this->type instanceof IntEnum || $this->type instanceof StringEnum) {
            Assertion::notNull($className, sprintf('Field [%s] requires a className.', $this->name));
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
        return $this->min;
    }

    /**
     * @return int
     */
    public function getMax()
    {
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
            return $this->isASingleValue() ? $this->type->getDefault() : [];
        }

        if ($this->default instanceof \Closure) {
            $default = call_user_func($this->default, $message);
            $this->guardDefault($default);
            if (null === $default) {
                return $this->isASingleValue() ? $this->type->getDefault() : [];
            }
            return $default;
        }

        return $this->default;
    }

    /**
     * @param mixed $default
     * @throws \Exception
     */
    private function guardDefault($default)
    {
        if ($this->isASingleValue()) {
            $this->guardValue($default);
        } else {
            Assertion::nullOrIsArray($default, sprintf('Field [%s] default must be an array.', $this->name));
            if ($this->isAMap()) {
                // todo: review, must a map be scalar too?
                if (null !== $default) {
                    Assertion::true(
                        ArrayUtils::isAssoc($default),
                        sprintf('Field [%s] default must be an associative array.', $this->name)
                    );
                }
            } else {
                Assertion::true(
                    $this->type->decodesToScalar(),
                    sprintf('Field [%s] must decode as a scalar to be used in a set or list.', $this->name)
                );
            }

            if (null !== $default) {
                Assertion::true(
                    !empty($default),
                    sprintf('Field [%s] default cannot be an empty array.', $this->name)
                );

                foreach ($default as $k => $v) {
                    Assertion::notNull(
                        $v,
                        sprintf('Field [%s] default for key [%s] cannot be null.', $this->name, $k)
                    );

                    if ($this->isAMap()) {
                        Assertion::string(
                            $k,
                            sprintf('Field [%s] default key [%s] must be a string.', $this->name, $k)
                        );
                    }
                    $this->guardValue($v);
                }
            }
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
     * @param mixed $value
     * @return mixed
     */
    public function encodeValue($value)
    {
        return $this->type->encode($value, $this);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function decodeValue($value)
    {
        return $this->type->decode($value, $this);
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