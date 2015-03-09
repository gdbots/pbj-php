<?php

namespace Gdbots\Pbj\Extension;

use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\Type as T;

class EventSchema extends Schema
{
    const EVENT_ID_FIELD_NAME = 'event_id';
    const MICROTIME_FIELD_NAME = 'microtime';
    const CORRELATOR_FIELD_NAME = 'correlator';

    /**
     * {@inheritdoc}
     */
    protected function getExtendedSchemaFields()
    {
        return [
            Fb::create(self::EVENT_ID_FIELD_NAME, T\TimeUuidType::create())
                ->required()
                ->build(),
            Fb::create(self::MICROTIME_FIELD_NAME, T\MicrotimeType::create())
                ->required()
                ->build(),
            Fb::create(self::CORRELATOR_FIELD_NAME, T\MessageRefType::create())
                ->build(),
        ];
    }
}
