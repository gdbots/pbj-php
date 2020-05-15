<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Field;
use Gdbots\Pbj\Schema;

class FieldAlreadyDefined extends SchemaException
{
    private Field $field;

    public function __construct(Schema $schema, string $fieldName)
    {
        $this->schema = $schema;
        $this->field = $this->schema->getField($fieldName);
        parent::__construct(
            sprintf(
                'Field [%s] is already defined on message [%s] and is not overridable.',
                $this->field->getName(),
                $this->schema->getClassName()
            )
        );
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
