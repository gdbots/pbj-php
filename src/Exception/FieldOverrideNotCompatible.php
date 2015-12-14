<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Field;
use Gdbots\Pbj\Schema;

class FieldOverrideNotCompatible extends SchemaException
{
    /** @var Field */
    private $existingField;

    /** @var Field */
    private $overrideField;

    /**
     * @param Schema $schema
     * @param string $fieldName
     * @param Field $overrideField
     */
    public function __construct(Schema $schema, $fieldName, Field $overrideField)
    {
        $this->schema = $schema;
        $this->existingField = $this->schema->getField($fieldName);
        parent::__construct(
            sprintf(
                'Field [%s] override for [%s] is not compatible. Name, Type, Rule and Required must match.',
                $this->existingField->getName(),
                $this->schema->getClassName()
            )
        );
    }

    /**
     * @return Field
     */
    public function getExistingField()
    {
        return $this->existingField;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->existingField->getName();
    }

    /**
     * @return Field
     */
    public function getOverrideField()
    {
        return $this->overrideField;
    }
}
