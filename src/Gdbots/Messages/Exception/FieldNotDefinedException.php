<?php

namespace Gdbots\Messages\Exception;

class FieldNotDefinedException extends \InvalidArgumentException
{
    private $type;
    private $fieldName;

    /**
     * @param string $type Fully qualified class name
     * @param string $fieldName Name of
     */
    public function __construct($type, $fieldName)
    {
        $this->type = $type;
        $this->fieldName = $fieldName;
        parent::__construct(sprintf('Field [%s] is not defined on message [%s].', $fieldName, $type));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }
}
