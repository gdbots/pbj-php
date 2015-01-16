<?php

namespace Gdbots\Pbj;

interface MessageResolver
{
    /**
     * @param string $className
     * @return Schema
     */
    public static function getSchema($className);

    /**
     * @param string $schemaId
     * @return string
     */
    public static function getClassName($schemaId);
}
