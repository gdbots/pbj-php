<?php

namespace Gdbots\Messages;

use Gdbots\Messages\Type\Type;

final class FieldDescriptor
{
    /** @var string */
    private $name;

    /** @var Type */
    private $type;

    /** @var mixed */
    private $default;

    /**
     * @param string $name
     * @param Type $type
     * @param mixed|null $default
     */
    public function __construct($name, Type $type, $default = null)
    {
        $this->name = $name;
        $this->type = $type;

        // todo: guard default value?
        $this->default = $default;
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
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }
}