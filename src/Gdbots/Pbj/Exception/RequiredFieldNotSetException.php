<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Field;
use Gdbots\Pbj\Message;

class RequiredFieldNotSetException extends SchemaException
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
        $this->schema = $type->schema();
        $this->field = $field;
        parent::__construct(sprintf('Required field [%s] must be set on message [%s].', $this->field->getName(), $this->schema->getClassName()));
    }

    /**
     * @return Message
     */
    public function get()
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
