<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Mixin;
use Gdbots\Pbj\Schema;

class MixinAlreadyAdded extends SchemaException
{
    /** @var Mixin */
    private $originalMixin;

    /** @var Mixin */
    private $duplicateMixin;

    /**
     * @param Schema $schema
     * @param Mixin $originalMixin
     * @param Mixin $duplicateMixin
     */
    public function __construct(Schema $schema, Mixin $originalMixin, Mixin $duplicateMixin)
    {
        $this->schema = $schema;
        $this->originalMixin = $originalMixin;
        $this->duplicateMixin = $duplicateMixin;
        parent::__construct(
            sprintf(
                'Mixin with id [%s] was already added from [%s] to message [%s].  ' .
                'You cannot add multiple versions of the same mixin.',
                $this->duplicateMixin->getId()->toString(),
                $this->originalMixin->getId()->toString(),
                $this->schema->getClassName()
            )
        );
    }

    /**
     * @return Mixin
     */
    public function getOriginalMixin()
    {
        return $this->originalMixin;
    }

    /**
     * @return Mixin
     */
    public function getDuplicateMixin()
    {
        return $this->duplicateMixin;
    }
}
