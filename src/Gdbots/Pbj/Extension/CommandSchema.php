<?php

namespace Gdbots\Pbj\Extension;

use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\Type as T;

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
            Fb::create(self::COMMAND_ID_FIELD_NAME, T\TimeUuidType::create())
                ->required()
                ->build(),
            Fb::create(self::MICROTIME_FIELD_NAME, T\MicrotimeType::create())
                ->required()
                ->build(),
        ];
    }
}
