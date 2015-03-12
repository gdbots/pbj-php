<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Pbj\AbstractMixin;
use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\HasCorrelator;
use Gdbots\Pbj\SchemaId;
use Gdbots\Pbj\Type as T;

final class RequestMixin extends AbstractMixin
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return SchemaId::fromString('pbj:gdbots:pbj:mixin:request:1-0-0');
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return [
            Fb::create(Request::REQUEST_ID_FIELD_NAME, T\UuidType::create())
                ->required()
                ->build(),
            Fb::create(Request::MICROTIME_FIELD_NAME, T\MicrotimeType::create())
                ->required()
                ->build(),
            Fb::create(HasCorrelator::CORRELATOR_FIELD_NAME, T\MessageRefType::create())
                ->build(),
        ];
    }
}
