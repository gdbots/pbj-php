<?php

namespace Gdbots\Messages;

use Gdbots\Common\Util\ArrayUtils;
use Gdbots\Messages\Enum\FieldRule;
use Gdbots\Messages\Type\IntEnum;
use Gdbots\Messages\Type\StringEnum;
use Gdbots\Messages\Type\Type;

// todo: implement toArray and JsonSerializable
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

    /** @var int */
    private $min = 0;

    /** @var int */
    private $max = 0;

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

        if ($this->type instanceof IntEnum || $this->type instanceof StringEnum) {
            Assertion::notNull($className, sprintf('Field [%s] requires a className.', $this->name), $this->name);
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
            Assertion::nullOrIsArray($default, sprintf('Field [%s] default must be an array.', $this->name), $this->name);
            if ($this->isAMap()) {
                // todo: review, must a map be scalar too?
                if (null !== $default) {
                    Assertion::true(ArrayUtils::isAssoc($default), sprintf('Field [%s] default must be an associative array.', $this->name), $this->name);
                }
            } else {
                Assertion::true($this->type->isScalar(), sprintf('Field [%s] must be scalar to be used in a set or list.', $this->name), $this->name);
            }

            if (null !== $default) {
                Assertion::true(!empty($default), sprintf('Field [%s] default cannot be an empty array.', $this->name), $this->name);
                foreach ($default as $k => $v) {
                    Assertion::notNull($v, sprintf('Field [%s] default for key [%s] cannot be null.', $this->name, $k), $this->name);
                    if ($this->isAMap()) {
                        Assertion::string($k, sprintf('Field [%s] default key [%s] must be a string.', $this->name, $k), $this->name);
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
            Assertion::notNull($value, sprintf('Field [%s] is required and cannot be null.', $this->name), $this->name);
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
}