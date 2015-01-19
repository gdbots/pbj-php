<?php

namespace Gdbots\Pbj;

interface MessageResolver
{
    /**
     * @param SchemaId $schemaId
     * @return string
     */
    public function getClassName(SchemaId $schemaId);
}
