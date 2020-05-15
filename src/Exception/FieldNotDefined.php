<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Schema;

class FieldNotDefined extends SchemaException
{
    private string $fieldName;

    public function __construct(Schema $schema, string $fieldName)
    {
        $this->schema = $schema;
        $this->fieldName = $fieldName;
        parent::__construct(
            sprintf(
                'Field [%s] is not defined on message [%s].',
                $this->fieldName,
                $this->schema->getClassName()
            )
        );
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
