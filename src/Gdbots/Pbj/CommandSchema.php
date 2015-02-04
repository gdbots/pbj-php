<?php

namespace Gdbots\Pbj;

class CommandSchema extends Schema
{
    const COMMAND_ID_FIELD_NAME = 'command_id';
    const MICROTIME_FIELD_NAME = 'microtime';

    /**
     * {@inheritdoc}
     */
    protected function getExtendedSchemaFields()
    {
        return [
            FieldBuilder::create(self::COMMAND_ID_FIELD_NAME, Type\TimeUuidType::create())
                ->required()
                ->build(),
            FieldBuilder::create(self::MICROTIME_FIELD_NAME, Type\MicrotimeType::create())
                ->required()
                ->build(),
        ];
    }
}
