<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Field;
use Gdbots\Pbj\Schema;

class FieldAlreadyDefinedException extends SchemaException
{
    /** @var Field */
    private $field;

    /**
     * @param Schema $schema
     * @param string $fieldName
     */
    public function __construct(Schema $schema, $fieldName)
    {
        $this->schema = $schema;
        $this->field = $this->schema->getField($fieldName);
        parent::__construct(
            sprintf('Field [%s] is already defined on message [%s].',
            $this->field->getName(),
            $this->schema->getClassName())
        );
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
