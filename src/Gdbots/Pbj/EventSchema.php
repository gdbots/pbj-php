<?php

namespace Gdbots\Pbj;

class EventSchema extends Schema
{
    const EVENT_ID_FIELD_NAME = 'event_id';
    const MICROTIME_FIELD_NAME = 'microtime';

    /**
     * {@inheritdoc}
     */
    protected function defineSchema()
    {
        return [
            FieldBuilder::create(self::EVENT_ID_FIELD_NAME, Type\TimeUuidType::create())
                ->required()
                ->build(),
            FieldBuilder::create(self::MICROTIME_FIELD_NAME, Type\MicrotimeType::create())
                ->required()
                ->build(),
        ];
    }
}
