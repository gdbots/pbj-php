<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Pbj\AbstractMixin;
use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\SchemaBuilder;
use Gdbots\Pbj\Type as T;

class CommandMixin extends AbstractMixin
{
    /**
     * {@inheritdoc}
     */
    public static function apply(SchemaBuilder $sb)
    {
        $sb
            ->addMixinField(
                Fb::create(Command::COMMAND_ID_FIELD_NAME, T\TimeUuidType::create())
                    ->required()
                    ->build()
            )
            ->addMixinField(
                Fb::create(Command::MICROTIME_FIELD_NAME, T\MicrotimeType::create())
                    ->required()
                    ->build()
            )
        ;
    }
}
