<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Schema;
use Gdbots\Pbj\SchemaId;

class MixinAlreadyAdded extends SchemaException
{
    /** @var SchemaId */
    private $originalMixinId;

    /** @var SchemaId */
    private $duplicateMixinId;

    /**
     * @param Schema $schema
     * @param SchemaId $originalMixinId
     * @param SchemaId $duplicateMixinId
     */
    public function __construct(Schema $schema, SchemaId $originalMixinId, SchemaId $duplicateMixinId)
    {
        $this->schema = $schema;
        $this->originalMixinId = $originalMixinId;
        $this->duplicateMixinId = $duplicateMixinId;
        parent::__construct(
            sprintf(
                'Mixin with id [%s] was already added from [%s] to message [%s].  ' .
                'You cannot add multiple versions of the same mixin.',
                $this->duplicateMixinId->toString(),
                $this->originalMixinId->toString(),
                $this->schema->getClassName()
            )
        );
    }

    /**
     * @return SchemaId
     */
    public function getOriginalMixinId()
    {
        return $this->originalMixinId;
    }

    /**
     * @return SchemaId
     */
    public function getDuplicateMixinId()
    {
        return $this->duplicateMixinId;
    }
}
