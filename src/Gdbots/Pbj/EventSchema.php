<?php

namespace Gdbots\Pbj;

class EventSchema extends Schema
{
    const EVENT_ID_FIELD_NAME = 'event_id';
    const MICROTIME_FIELD_NAME = 'microtime';
    const CORREL_ID_FIELD_NAME = 'correl_id';

    /**
     * {@inheritdoc}
     */
    protected function getExtendedSchemaFields()
    {
        return [
            FieldBuilder::create(self::EVENT_ID_FIELD_NAME, Type\TimeUuidType::create())
                ->required()
                ->build(),
            FieldBuilder::create(self::MICROTIME_FIELD_NAME, Type\MicrotimeType::create())
                ->required()
                ->build(),
            FieldBuilder::create(self::CORREL_ID_FIELD_NAME, Type\UuidType::create())
                ->useTypeDefault(false)
                ->build(),
        ];
    }
}