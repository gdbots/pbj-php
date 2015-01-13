<?php

namespace Gdbots\Messages\Exception;

use Gdbots\Messages\Schema;

class FieldNotDefinedException extends SchemaException
{
    /** @var string */
    private $fieldName;

    /**
     * @param Schema $schema
     * @param string $fieldName
     */
    public function __construct(Schema $schema, $fieldName)
    {
        $this->schema = $schema;
        $this->fieldName = $fieldName;
        parent::__construct(sprintf('Field [%s] is not defined on message [%s].', $this->fieldName, $this->schema->getClassName()));
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }
}
