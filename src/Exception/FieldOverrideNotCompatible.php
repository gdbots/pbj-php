<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Field;
use Gdbots\Pbj\Schema;

class FieldOverrideNotCompatible extends SchemaException
{
    private Field $existingField;
    private Field $overrideField;

    public function __construct(Schema $schema, string $fieldName, Field $overrideField)
    {
        $this->schema = $schema;
        $this->existingField = $this->schema->getField($fieldName);
        $this->overrideField = $overrideField;
        parent::__construct(
            sprintf(
                'Field [%s] override for [%s] is not compatible. Name, Type, Rule and Required must match.',
                $this->existingField->getName(),
                $this->schema->getClassName()
            )
        );
    }

    public function getExistingField(): Field
    {
        return $this->existingField;
    }

    public function getFieldName(): string
    {
        return $this->existingField->getName();
    }

    public function getOverrideField(): Field
    {
        return $this->overrideField;
    }
}
