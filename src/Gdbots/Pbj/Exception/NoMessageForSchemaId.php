<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\SchemaId;

class NoMessageForSchemaId extends \LogicException implements GdbotsPbjException
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
            sprintf(
                'MessageResolver is unable to resolve schema id [%s] ' .
                'using curie [%s] to a class name.',
                $schemaId->toString(),
                $schemaId->getCurieWithMajorRev()
            )
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

