<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Schema;
use Gdbots\Pbj\SchemaId;

class InvalidResolvedSchema extends SchemaException
{
    /** @var SchemaId */
    private $invalidSchemaId;

    /**
     * @param Schema $schema
     * @param SchemaId $invalidSchemaId
     */
    public function __construct(Schema $schema, SchemaId $invalidSchemaId)
    {
        $this->schema = $schema;
        $this->invalidSchemaId = $invalidSchemaId;
        parent::__construct(
            sprintf(
                'Schema id [%s] was resolved to [%s] but that message has a schema id of [%s].  They must match.',
                $this->invalidSchemaId->toString(),
                $this->schema->getClassName(),
                $this->schema->getId()->toString()
            )
        );
    }

    /**
     * @return SchemaId
     */
    public function getInvalidSchemaId()
    {
        return $this->invalidSchemaId;
    }
}
