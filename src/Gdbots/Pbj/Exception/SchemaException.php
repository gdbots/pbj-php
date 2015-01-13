<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Schema;

abstract class SchemaException extends \LogicException implements GdbotsPbjException
{
    /** @var Schema */
    protected $schema;

    /**
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }
}
