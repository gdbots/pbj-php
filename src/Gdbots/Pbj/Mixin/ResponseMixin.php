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
            Fb::create('response_id', T\UuidType::create())
                ->required()
                ->build(),
            Fb::create('microtime', T\MicrotimeType::create())
                ->required()
                ->build(),
            Fb::create('request_ref', T\MessageRefType::create())
                ->build(),
            Fb::create('correlator', T\MessageRefType::create())
                ->build(),
        ];
    }
}
