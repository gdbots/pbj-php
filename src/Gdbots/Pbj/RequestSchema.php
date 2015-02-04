<?php

namespace Gdbots\Pbj;

class RequestSchema extends Schema
{
    const REQUEST_ID_FIELD_NAME = 'request_id';
    const MICROTIME_FIELD_NAME = 'microtime';

    /**
     * {@inheritdoc}
     */
    protected function getExtendedSchemaFields()
    {
        return [
            FieldBuilder::create(self::REQUEST_ID_FIELD_NAME, Type\UuidType::create())
                ->required()
                ->build(),
            FieldBuilder::create(self::MICROTIME_FIELD_NAME, Type\MicrotimeType::create())
                ->required()
                ->build(),
        ];
    }
}
