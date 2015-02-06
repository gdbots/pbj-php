<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Schema;
use Gdbots\Pbj\SchemaId;

class InvalidResolvedSchema extends SchemaException
{
    /** @var SchemaId */
    private $resolvedSchemaId;

    /** @var string */
    private $resolvedClassName;

    /**
     * @param Schema $schema
     * @param SchemaId $resolvedSchemaId
     * @param string $resolvedClassName
     */
    public function __construct(Schema $schema, SchemaId $resolvedSchemaId, $resolvedClassName)
    {
        $this->schema = $schema;
        $this->resolvedSchemaId = $resolvedSchemaId;
        $this->resolvedClassName = $resolvedClassName;
        parent::__construct(
            sprintf(
                'Schema id [%s] with resolver key [%s] was resolved to [%s] but ' .
                'that message has a resolver key of [%s].  They must match.',
                $this->resolvedSchemaId->toString(),
                $this->resolvedSchemaId->getResolverKey(),
                $resolvedClassName,
                $this->schema->getId()->getResolverKey()
            )
        );
    }

    /**
     * @return SchemaId
     */
    public function getResolvedSchemaId()
    {
        return $this->resolvedSchemaId;
    }

    /**
     * @return string
     */
    public function getResolvedClassName()
    {
        return $this->resolvedClassName;
    }
}
