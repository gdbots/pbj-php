<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\SchemaId;

class NoMessageForSchemaIdException extends \LogicException implements GdbotsPbjException
{
    /** @var SchemaId */
    private $schemaId;

    /**
     * @param SchemaId $schemaId
     */
    public function __construct(SchemaId $schemaId)
    {
        $this->schemaId = $schemaId;
        parent::__construct(
            sprintf('MessageResolver is unable to resolve [%s] to a class name.', $schemaId->toString())
        );
    }

    /**
     * @return SchemaId
     */
    public function getSchemaId()
    {
        return $this->schemaId;
    }
}

