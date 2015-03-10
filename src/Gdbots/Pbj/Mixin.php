<?php

namespace Gdbots\Pbj;

interface Mixin
{
    /**
     * Adds the mixin schema to the builder.
     *
     * @param SchemaBuilder $sb
     */
    public static function apply(SchemaBuilder $sb);
}
