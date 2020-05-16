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

    /** @var array */
    private $anyOfClassNames;

    /** @var \Closure */
    private $assertion;

    /** @var bool */
    private $overridable = false;

    /**
     * @param string $name
     * @param Type   $type
     */
    final private function __construct($name, Type $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @param string $name
     * @param Type   $type
     *
     * @return self
     */
    public static function create($name, Type $type): self
    {
        $builder = new static($name, $type);
        return $builder;
    }

    /**
     * @return self
     */
    public function required(): self
    {
        $this->required = true;
        return $this;
    }

    /**
     * @return self
     */
    public function optional(): self
    {
        $this->required = false;
        return $this;
    }

    /**
     * @return self
     */
    public function asASingleValue(): self
    {
        $this->rule = FieldRule::A_SINGLE_VALUE();
        return $this;
    }

    /**
     * @return self
     */
    public function asASet(): self
    {
        $this->rule = FieldRule::A_SET();
        return $this;
    }

    /**
     * @return self
     */
    public function asAList(): self
    {
        $this->rule = FieldRule::A_LIST();
        return $this;
    }

    /**
     * @return self
     */
    public function asAMap(): self
    {
        $this->rule = FieldRule::A_MAP();
        return $this;
    }

    /**
     * @param int $minLength
     *
     * @return self
     */
    public function minLength($minLength): self
    {
        $this->minLength = (int)$minLength;
        return $this;
    }

    /**
     * @param int $maxLength
     *
     * @return self
     */
    public function maxLength($maxLength): self
    {
        $this->maxLength = (int)$maxLength;
        return $this;
    }

    /**
     * @param string $pattern
     *
     * @return self
     */
    public function pattern($pattern): self
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * @param string $format
     *
     * @return self
     */
    public function format($format): self
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @param int $min
     *
     * @return self
     */
    public function min($min): self
    {
        $this->min = (int)$min;
        return $this;
    }

    /**
     * @param int $max
     *
     * @return self
     */
    public function max($max): self
    {
        $this->max = (int)$max;
        return $this;
    }

    /**
     * @param int $precision
     *
     * @return self
     */
    public function precision($precision): self
    {
        $this->precision = (int)$precision;
        return $this;
    }

    /**
     * @param int $scale
     *
     * @return self
     */
    public function scale($scale): self
    {
        $this->scale = (int)$scale;
        return $this;
    }

    /**
     * @param mixed $default
     *
     * @return self
     */
    public function withDefault($default): self
    {
        $this->default = $default;
        return $this;
    }

    /**
     * @param bool $useTypeDefault
     *
     * @return self
     */
    public function useTypeDefault($useTypeDefault): self
    {
        $this->useTypeDefault = (bool)$useTypeDefault;
        return $this;
    }

    /**
     * @param string $className
     *
     * @return self
     */
    public function className($className): self
    {
        $this->className = $className;
        $this->anyOfClassNames = null;
        return $this;
    }

    /**
     * @param array $anyOfClassNames
     *
     * @return self
     */
    public function anyOfClassNames(array $anyOfClassNames): self
    {
        $this->anyOfClassNames = $anyOfClassNames;
        $this->className = null;
        return $this;
    }

    /**
     * @param \Closure $assertion
     *
     * @return self
     */
    public function assertion(\Closure $assertion): self
    {
        $this->assertion = $assertion;
        return $this;
    }

    /**
     * @param bool $overridable
     *
     * @return self
     */
    public function overridable($overridable): self
    {
        $this->overridable = (bool)$overridable;
        return $this;
    }

    /**
     * @return Field
     */
    public function build(): Field
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
            $this->anyOfClassNames,
            $this->assertion,
            $this->overridable
        );
    }
}
