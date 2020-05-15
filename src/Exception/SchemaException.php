<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Schema;

abstract class SchemaException extends \LogicException implements GdbotsPbjException
{
    protected Schema $schema;

    public function getSchema(): Schema
    {
        return $this->schema;
    }
}
