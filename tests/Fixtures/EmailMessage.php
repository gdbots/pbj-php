<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj\Fixtures;

use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\Enum\Format;
use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\Message;
use Gdbots\Pbj\MessageResolver;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\Type as T;
use Gdbots\Pbj\WellKnown\MessageRef;
use Gdbots\Tests\Pbj\Fixtures\Enum\Priority;
use Gdbots\Tests\Pbj\Fixtures\Enum\Provider;

final class EmailMessage extends AbstractMessage
{
    protected static function defineSchema(): Schema
    {
        $schema = new Schema('pbj:gdbots:tests.pbj:fixtures:email-message:1-0-0', __CLASS__,
            [
                Fb::create('id', T\TimeUuidType::create())
                    ->useTypeDefault(false)
                    ->required()
                    ->build(),
                Fb::create('from_name', T\StringType::create())
                    ->build(),
                Fb::create('from_email', T\StringType::create())
                    ->required()
                    ->format(Format::EMAIL)
                    ->build(),
                Fb::create('subject', T\StringType::create())
                    ->withDefault(function (EmailMessage $message = null) {
                        if (!$message) {
                            return null;
                        }
                        return implode(',', $message->get('labels', [])) . ' test';
                    })
                    ->build(),
                Fb::create('body', T\StringType::create())->build(),
                Fb::create('priority', T\IntEnumType::create())
                    ->required()
                    ->className(Priority::class)
                    ->withDefault(Priority::NORMAL)
                    ->build(),
                Fb::create('sent', T\BooleanType::create())->build(),
                Fb::create('date_sent', T\DateTimeType::create())->build(),
                Fb::create('microtime_sent', T\MicrotimeType::create())->build(),
                Fb::create('provider', T\StringEnumType::create())
                    ->className(Provider::class)
                    ->withDefault(Provider::GMAIL)
                    ->build(),
                Fb::create('labels', T\StringType::create())
                    ->format(Format::HASHTAG)
                    ->asASet()
                    ->build(),
                Fb::create('unsubscribe_url', T\TextType::create())
                    ->format(Format::URL)
                    ->build(),
                Fb::create('nested', T\MessageType::create())
                    ->anyOfCuries([
                        NestedMessage::schema()->getCurie()->toString(),
                    ])
                    ->build(),
                Fb::create('enum_in_set', T\StringEnumType::create())
                    ->className(Provider::class)
                    ->asASet()
                    ->build(),
                Fb::create('enum_in_list', T\StringEnumType::create())
                    ->className(Provider::class)
                    ->asAList()
                    ->build(),
                Fb::create('any_of_message', T\MessageType::create())
                    ->className(Message::class)
                    ->asAList()
                    ->build(),
                Fb::create('dynamic_fields', T\DynamicFieldType::create())
                    ->asAList()
                    ->build(),
                Fb::create('node_ref', T\NodeRefType::create())
                    ->build(),
            ],
            [
                'gdbots:tests.pbj:mixin:many:v1',
                'gdbots:tests.pbj:mixin:many',
                'gdbots:tests.pbj:mixin:one:v1',
                'gdbots:tests.pbj:mixin:one',
            ]
        );

        MessageResolver::registerSchema($schema);
        return $schema;
    }

    public function generateMessageRef(?string $tag = null): MessageRef
    {
        return new MessageRef(static::schema()->getCurie(), $this->get('id'), $tag);
    }

    public function getUriTemplateVars(): array
    {
        return ['id' => $this->get('id')->toString()];
    }
}
