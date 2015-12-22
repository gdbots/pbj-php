<?php

namespace Gdbots\Tests\Pbj\Fixtures;

use Gdbots\Common\GeoPoint;
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

    /**
     * @return string
     */
    public function getTest1()
    {
        return $this->get('test1');
    }

    /**
     * @param string $test1
     * @return self
     */
    public function setTest1($test1)
    {
        return $this->setSingleValue('test1', $test1);
    }

    /**
     * @return array
     */
    public function getTest2()
    {
        return $this->get('test2') ?: [];
    }

    /**
     * @param int $test2
     * @return self
     */
    public function addTest2($test2)
    {
        return $this->addToSet('test2', [$test2]);
    }

    /**
     * @param int $test2
     * @return self
     */
    public function removeTest2($test2)
    {
        return $this->removeFromSet('test2', [$test2]);
    }

    /**
     * @return GeoPoint
     */
    public function getLocation()
    {
        return $this->get('location');
    }

    /**
     * @param GeoPoint $location
     * @return self
     */
    public function setLocation(GeoPoint $location)
    {
        return $this->setSingleValue('location', $location);
    }

    /**
     * @return MessageRef[]
     */
    public function getRefs()
    {
        return $this->get('location');
    }

    /**
     * @param MessageRef $ref
     * @return self
     */
    public function addRef(MessageRef $ref)
    {
        return $this->addToSet('refs', [$ref]);
    }
}
