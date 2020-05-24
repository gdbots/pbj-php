<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Schema;
use Gdbots\Pbj\SchemaId;

final class InvalidResolvedSchema extends SchemaException
{
    private SchemaId $resolvedSchemaId;
    private string $resolvedClassName;

    public function __construct(Schema $schema, SchemaId $resolvedSchemaId, string $resolvedClassName)
    {
        $this->schema = $schema;
        $this->resolvedSchemaId = $resolvedSchemaId;
        $this->resolvedClassName = $resolvedClassName;
        parent::__construct(
            sprintf(
                'Schema id [%s] with curie [%s] was resolved to [%s] but ' .
                'that message has a curie of [%s].  They must match.',
                $this->resolvedSchemaId->toString(),
                $this->resolvedSchemaId->getCurieMajor(),
                $resolvedClassName,
                $this->schema->getId()->getCurieMajor()
            )
        );
    }

    public function getResolvedSchemaId(): SchemaId
    {
        return $this->resolvedSchemaId;
    }

    public function getResolvedClassName(): string
    {
        return $this->resolvedClassName;
    }
}
