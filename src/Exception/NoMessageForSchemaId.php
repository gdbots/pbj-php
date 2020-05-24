<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\SchemaId;

final class NoMessageForSchemaId extends \LogicException implements GdbotsPbjException
{
    private SchemaId $schemaId;

    public function __construct(SchemaId $schemaId)
    {
        $this->schemaId = $schemaId;
        parent::__construct(
            sprintf(
                'MessageResolver is unable to resolve schema id [%s] ' .
                'using curie [%s] to a class name.',
                $schemaId->toString(),
                $schemaId->getCurieMajor()
            )
        );
    }

    public function getSchemaId(): SchemaId
    {
        return $this->schemaId;
    }
}
