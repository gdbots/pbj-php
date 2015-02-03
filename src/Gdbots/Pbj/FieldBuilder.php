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
    private $minLength;

    /** @var int */
    private $maxLength;

    /** @var string */
    private $pattern;

    /** @var string */
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
     */
    final private function __construct($name, Type $type)
    {
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
        $this->rule = FieldRule::A_SET();
        return $this;
    }

    /**
     * @return self
     */
    public function asAList()
    {
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
     * @return self
     */
    public function minLength($minLength)
    {
        $this->minLength = (int) $minLength;
        return $this;
    }

    /**
     * @param int $maxLength
     * @return self
     */
    public function maxLength($maxLength)
    {
        $this->maxLength = (int) $maxLength;
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
     * @return self
     */
    public function min($min)
    {
        $this->min = (int) $min;
        return $this;
    }

    /**
     * @param int $max
     * @return self
     */
    public function max($max)
    {
        $this->max = (int) $max;
        return $this;
    }

    /**
     * @param int $precision
     * @return self
     */
    public function precision($precision)
    {
        $this->precision = (int) $precision;
        return $this;
    }

    /**
     * @param int $scale
     * @return self
     */
    public function scale($scale)
    {
        $this->scale = (int) $scale;
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
     * @param bool $useTypeDefault
     * @return self
     */
    public function useTypeDefault($useTypeDefault)
    {
        $this->useTypeDefault = (bool) $useTypeDefault;
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
            $this->useTypeDefault,
            $this->className,
            $this->assertion
        );
    }
}