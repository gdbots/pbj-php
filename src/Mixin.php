<?php

namespace Gdbots\Pbj;

interface Mixin
{
    /**
     * @return Mixin
     */
    public static function create();

    /**
     * Returns the id for this mixin.
     * @return SchemaId
     */
    public function getId();

    /**
     * Returns an array of fields that the mixin provides.
     * @return Field[]
     */
    public function getFields();
}
