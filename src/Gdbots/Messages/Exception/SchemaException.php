<?php

namespace Gdbots\Messages\Exception;

use Gdbots\Messages\Schema;

abstract class SchemaException extends \LogicException implements GdbotsMessagesException
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
