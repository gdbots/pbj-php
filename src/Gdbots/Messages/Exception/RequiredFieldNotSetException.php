<?php

namespace Gdbots\Messages\Exception;

use Gdbots\Messages\Field;
use Gdbots\Messages\Message;

class RequiredFieldNotSetException extends \LogicException
{
    /** @var Message */
    private $type;

    /** @var Field */
    private $field;

    /**
     * @param Message $type Fully qualified class name
     * @param string Field $field
     */
    public function __construct(Message $type, Field $field)
    {
        $this->type = $type;
        $this->field = $field;
        parent::__construct(sprintf('Required field [%s] must be set on message [%s].', $field->getName(), get_class($type)));
    }

    /**
     * @return Message
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->field->getName();
    }
}
