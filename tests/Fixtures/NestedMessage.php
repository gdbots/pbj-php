<?php

namespace Gdbots\Tests\Pbj\Fixtures;

use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\MessageRef;
use Gdbots\Pbj\MessageResolver;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\Type as T;

final class NestedMessage extends AbstractMessage
{
    /**
     * @return Schema
     */
    protected static function defineSchema()
    {
        $schema = new Schema('pbj:gdbots:tests.pbj:fixtures:nested-message:1-0-0', __CLASS__, [
            Fb::create('test1', T\StringType::create())->build(),
            Fb::create('test2', T\IntType::create())->asASet()->build(),
            Fb::create('location', T\GeoPointType::create())->build(),
            Fb::create('refs', T\MessageRefType::create())->asASet()->build(),
        ]);

        MessageResolver::registerSchema($schema);
        return $schema;
    }

    /**
     * {@inheritdoc}
     */
    public function generateMessageRef($tag = null)
    {
        return new MessageRef(static::schema()->getCurie(), null, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function getUriTemplateVars()
    {
        return [];
    }
}
