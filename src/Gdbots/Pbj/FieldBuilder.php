<?php

namespace Gdbots\Pbj;

use Gdbots\Pbj\Enum\FieldRule;
use Gdbots\Pbj\Type\Type;

final class FieldBuilder
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

    /** @var string */
    private $pattern;

    /** @var string */
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
     */
    final private function __construct($name, Type $type)
    {
        Assertion::string($name);
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @param string $name
     * @param Type $type
     * @return self
     */
    public static function create($name, Type $type)
    {
        $builder = new static($name, $type);
        return $builder;
    }

    /**
     * @return self
     */
    public function required()
    {
        $this->required = true;
        return $this;
    }

    /**
     * @return self
     */
    public function optional()
    {
        $this->required = false;
        return $this;
    }

    /**
     * @return self
     */
    public function asASingleValue()
    {
        $this->rule = FieldRule::A_SINGLE_VALUE();
        return $this;
    }

    /**
     * @return self
     */
    public function asASet()
    {
        Assertion::true(
            $this->type->decodesToScalar(),
            sprintf('Field [%s] must decode as a scalar to be used in a set.', $this->name)
        );
        $this->rule = FieldRule::A_SET();
        return $this;
    }

    /**
     * @return self
     */
    public function asAList()
    {
        Assertion::true(
            $this->type->decodesToScalar(),
            sprintf('Field [%s] must decode as a scalar to be used in a list.', $this->name)
        );
        $this->rule = FieldRule::A_LIST();
        return $this;
    }

    /**
     * @return self
     */
    public function asAMap()
    {
        $this->rule = FieldRule::A_MAP();
        return $this;
    }

    /**
     * @param int $minLength
     * @param int $maxLength
     * @return self
     */
    public function betweenLength($minLength = 0, $maxLength = 0)
    {
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
        return $this;
    }

    /**
     * @param string $pattern
     * @return self
     */
    public function pattern($pattern)
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * @param string $format
     * @return self
     */
    public function format($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @param int $min
     * @param int $max
     * @return self
     */
    public function range($min = 0, $max = 0)
    {
        $this->min = $min;
        $this->max = $max;
        return $this;
    }

    /**
     * @param int $precision
     * @param int $scale
     * @return self
     */
    public function precision($precision = 10, $scale = 0)
    {
        $this->precision = $precision;
        $this->scale = $scale;
        return $this;
    }

    /**
     * @param mixed $default
     * @return self
     */
    public function withDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * @param string $className
     * @return self
     */
    public function className($className)
    {
        $this->className = $className;
        return $this;
    }

    /**
     * @param \Closure $assertion
     * @return self
     */
    public function assertion(\Closure $assertion)
    {
        $this->assertion = $assertion;
        return $this;
    }

    /**
     * @return Field
     */
    public function build()
    {
        if (null === $this->rule) {
            $this->rule = FieldRule::A_SINGLE_VALUE();
        }

        return new Field(
            $this->name,
            $this->type,
            $this->rule,
            $this->required,
            $this->minLength,
            $this->maxLength,
            $this->pattern,
            $this->format,
            $this->min,
            $this->max,
            $this->precision,
            $this->scale,
            $this->default,
            $this->className,
            $this->assertion
        );
    }
}