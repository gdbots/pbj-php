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
     *
     * @return SchemaId
     */
    public function getId();

    /**
     * Returns an array of fields that the mixin provides.
     *
     * @return Field[]
     */
    public function getFields();

    /**
     * Shortcut to resolving a mixin to one concrete schema.
     *
     * @param string $inPackage
     * @param string $inCategory
     *
     * @return Schema
     */
    public static function findOne($inPackage = null, $inCategory = null);

    /**
     * Shortcut to resolving a mixin to all concrete schemas.
     *
     * @param string $inPackage
     * @param string $inCategory
     *
     * @return Schema[]
     */
    public static function findAll($inPackage = null, $inCategory = null);
}
