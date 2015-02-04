<?php

namespace Gdbots\Pbj;

class ResponseSchema extends Schema
{
    const RESPONSE_ID_FIELD_NAME = 'response_id';
    const MICROTIME_FIELD_NAME = 'microtime';
    const REQUEST_ID_FIELD_NAME = 'request_id';

    /**
     * {@inheritdoc}
     */
    protected function getExtendedSchemaFields()
    {
        return [
            FieldBuilder::create(self::RESPONSE_ID_FIELD_NAME, Type\UuidType::create())
                ->required()
                ->build(),
            FieldBuilder::create(self::MICROTIME_FIELD_NAME, Type\MicrotimeType::create())
                ->required()
                ->build(),
            FieldBuilder::create(self::REQUEST_ID_FIELD_NAME, Type\UuidType::create())
                ->required()
                ->useTypeDefault(false)
                ->build(),
        ];
    }
}
