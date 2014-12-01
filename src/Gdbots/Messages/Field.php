<?php

namespace Gdbots\Messages;

use Assert\Assertion;
use Gdbots\Messages\Enum\FieldRule;
use Gdbots\Messages\Type\IntEnumType;
use Gdbots\Messages\Type\StringEnumType;
use Gdbots\Messages\Type\Type;

final class Field
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
     * @param FieldRule $rule
     * @param bool $required
     * @param mixed|null $default
     * @param string $className
     * @param \Closure $assertion = null
     */
    public function __construct(
            $name,
            Type $type,
            FieldRule $rule = null,
            $required = false,
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
        $this->default = $default;
        $this->className = $className;
        $this->assertion = $assertion;

        if ($this->type instanceof IntEnumType || $this->type instanceof StringEnumType) {
            Assertion::notNull($className, sprintf('Field [%s] requires a className.', $this->name), $this->name);
        }

        // todo: handle multi-valued fields
        if ($this->hasDefault()) {
            $this->guardValue($default);
        } else {
            $this->default = $this->type->getDefault();
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
     * @return bool
     */
    public function hasDefault()
    {
        return null !== $this->default;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
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
            Assertion::notNull($value, sprintf('Field [%s] is required and cannot be null.', $this->name), $this->name);
        }

        if (null !== $value) {
            $this->type->guard($value, $this);
        }

        if (null !== $this->assertion) {
            call_user_func($this->assertion, $value, $this);
        }
    }
}