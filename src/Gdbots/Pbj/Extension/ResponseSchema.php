<?php

namespace Gdbots\Pbj\Extension;

use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\Type as T;

class ResponseSchema extends Schema
{
    const RESPONSE_ID_FIELD_NAME = 'response_id';
    const MICROTIME_FIELD_NAME = 'microtime';
    const REQUEST_ID_FIELD_NAME = 'request_id';
    const CORRELATOR_FIELD_NAME = 'correlator';

    /**
     * {@inheritdoc}
     */
    protected function getExtendedSchemaFields()
    {
        return [
            Fb::create(self::RESPONSE_ID_FIELD_NAME, T\UuidType::create())
                ->required()
                ->build(),
            Fb::create(self::MICROTIME_FIELD_NAME, T\MicrotimeType::create())
                ->required()
                ->build(),
            Fb::create(self::REQUEST_ID_FIELD_NAME, T\UuidType::create())
                ->required()
                ->useTypeDefault(false)
                ->build(),
            Fb::create(self::CORRELATOR_FIELD_NAME, T\MessageRefType::create())
                ->build(),
        ];
    }
}
