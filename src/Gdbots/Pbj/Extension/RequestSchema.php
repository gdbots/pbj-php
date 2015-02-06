<?php

namespace Gdbots\Pbj\Extension;

use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\Type as T;

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
            Fb::create(self::REQUEST_ID_FIELD_NAME, T\UuidType::create())
                ->required()
                ->build(),
            Fb::create(self::MICROTIME_FIELD_NAME, T\MicrotimeType::create())
                ->required()
                ->build(),
        ];
    }
}
