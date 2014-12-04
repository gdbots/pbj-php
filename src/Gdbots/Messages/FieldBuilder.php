<?php

namespace Gdbots\Messages;

use Assert\Assertion;
use Gdbots\Messages\Enum\FieldRule;
use Gdbots\Messages\Type\Type;

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
                $this->type->isScalar(),
                sprintf('Field [%s] must be scalar to be used in a set.', $this->name),
                $this->name
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
                $this->type->isScalar(),
                sprintf('Field [%s] must be scalar to be used in a list.', $this->name),
                $this->name
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
    public function usingClass($className)
    {
        $this->className = $className;
        return $this;
    }

    /**
     * @param \Closure $assertion
     * @return self
     */
    public function withAssertion(\Closure $assertion)
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
            $this->default,
            $this->className,
            $this->assertion
        );
    }
}