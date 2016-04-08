<?php

namespace Gdbots\Tests\Pbj\Fixtures;

use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\Enum\Format;
use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\MessageRef;
use Gdbots\Pbj\MessageResolver;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\Type as T;
use Gdbots\Tests\Pbj\Fixtures\Enum\Priority;
use Gdbots\Tests\Pbj\Fixtures\Enum\Provider;

final class EmailMessage extends AbstractMessage
{
    /**
     * @return Schema
     */
    protected static function defineSchema()
    {
        $schema = new Schema('pbj:gdbots:tests.pbj:fixtures:email-message:1-0-0', __CLASS__,
            [
                Fb::create('id', T\TimeUuidType::create())
                    //->useTypeDefault(false)
                    ->required()
                    ->build(),
                Fb::create('from_name', T\StringType::create())
                    ->build(),
                Fb::create('from_email', T\StringType::create())
                    ->required()
                    ->format('email')
                    ->build(),
                Fb::create('subject', T\StringType::create())
                    ->withDefault(function (EmailMessage $message = null) {
                        // closure func default spice or gtfo and use named automagic defaults?
                        if (!$message) {
                            return null;
                        }
                        return implode(',', $message->get('labels', [])) . ' test';
                    })
                    ->build(),
                Fb::create('body', T\StringType::create())->build(),
                Fb::create('priority', T\IntEnumType::create())
                    ->required()
                    ->className('Gdbots\Tests\Pbj\Fixtures\Enum\Priority')
                    ->withDefault(Priority::NORMAL)
                    ->build(),
                Fb::create('sent', T\BooleanType::create())->build(),
                Fb::create('date_sent', T\DateTimeType::create())->build(),
                Fb::create('microtime_sent', T\MicrotimeType::create())->build(),
                Fb::create('provider', T\StringEnumType::create())
                    ->className('Gdbots\Tests\Pbj\Fixtures\Enum\Provider')
                    ->withDefault(Provider::GMAIL())
                    ->build(),
                Fb::create('labels', T\StringType::create())
                    ->format(Format::HASHTAG())
                    ->asASet()
                    ->build(),
                Fb::create('nested', T\MessageType::create())
                    ->className('Gdbots\Tests\Pbj\Fixtures\NestedMessage')
                    ->build(),
                Fb::create('enum_in_set', T\StringEnumType::create())
                    ->className('Gdbots\Tests\Pbj\Fixtures\Enum\Provider')
                    ->asASet()
                    ->build(),
                Fb::create('enum_in_list', T\StringEnumType::create())
                    ->className('Gdbots\Tests\Pbj\Fixtures\Enum\Provider')
                    ->asAList()
                    ->build(),
                Fb::create('any_of_message', T\MessageType::create())
                    ->className('Gdbots\Pbj\Message')
                    ->asAList()
                    ->build(),
            ]
        );

        MessageResolver::registerSchema($schema);
        return $schema;
    }

    /**
     * {@inheritdoc}
     */
    public function generateMessageRef($tag = null)
    {
        return new MessageRef(static::schema()->getCurie(), $this->get('id'), $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function getUriTemplateVars()
    {
        return ['id' => $this->get('id')->toString()];
    }
}
