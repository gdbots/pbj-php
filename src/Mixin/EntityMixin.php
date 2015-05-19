<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\AbstractMixin;
use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\SchemaId;
use Gdbots\Pbj\Type as T;

final class EntityMixin extends AbstractMixin
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return SchemaId::fromString('pbj:gdbots:pbj:mixin:entity:1-0-0');
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return [
            Fb::create('_id', T\IdentifierType::create())
                ->required()
                ->className('Gdbots\Identifiers\UuidIdentifier')
                ->withDefault(function () {
                    return UuidIdentifier::generate();
                })
                ->build(),
            Fb::create('etag', T\StringType::create())
                ->pattern('/^[A-Za-z0-9_\-]+$/')
                ->maxLength(100)
                ->build(),
            Fb::create('created_at', T\MicrotimeType::create())
                ->required()
                ->build(),
            Fb::create('updated_at', T\MicrotimeType::create())
                ->useTypeDefault(false)
                ->build(),
        ];
    }
}
