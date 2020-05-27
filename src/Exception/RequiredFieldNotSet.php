<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Field;
use Gdbots\Pbj\Message;

final class RequiredFieldNotSet extends SchemaException
{
    private Message $type;
    private Field $field;

    public function __construct(Message $type, Field $field)
    {
        $this->type = $type;
        $this->schema = $type->schema();
        $this->field = $field;
        parent::__construct(
            sprintf(
                'Required field [%s] must be set on message [%s].',
                $this->field->getName(),
                $this->schema->getClassName()
            )
        );
    }

    public function getType(): Message
    {
        return $this->type;
    }

    public function getField(): Field
    {
        return $this->field;
    }

    public function getFieldName(): string
    {
        return $this->field->getName();
    }
}
