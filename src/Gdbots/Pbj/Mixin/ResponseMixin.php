<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Pbj\AbstractMixin;
use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\SchemaId;
use Gdbots\Pbj\Type as T;

final class ResponseMixin extends AbstractMixin
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return SchemaId::fromString('pbj:gdbots:pbj:mixin:response:1-0-0');
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return [
            Fb::create(Response::RESPONSE_ID_FIELD_NAME, T\UuidType::create())
                ->required()
                ->build(),
            Fb::create(Response::MICROTIME_FIELD_NAME, T\MicrotimeType::create())
                ->required()
                ->build(),
            Fb::create(Response::REQUEST_ID_FIELD_NAME, T\UuidType::create())
                ->required()
                ->useTypeDefault(false)
                ->build(),
            Fb::create(Response::CORRELATOR_FIELD_NAME, T\MessageRefType::create())
                ->build(),
        ];
    }
}
