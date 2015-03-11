<?php

namespace Gdbots\Tests\Pbj\Fixtures;

use Gdbots\Common\GeoPoint;
use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\MessageResolver;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\Type as T;

final class NestedMessage extends AbstractMessage
{
    const TEST1_FIELD_NAME = 'test1';
    const TEST2_FIELD_NAME = 'test2';
    const LOCATION_FIELD_NAME = 'location';

    /**
     * @return Schema
     */
    protected static function defineSchema()
    {
        $schema = new Schema('pbj:gdbots:tests.pbj:fixtures:nested-message:1-0-0', __CLASS__, [
            Fb::create(self::TEST1_FIELD_NAME, T\StringType::create())->build(),
            Fb::create(self::TEST2_FIELD_NAME, T\IntType::create())->asASet()->build(),
            Fb::create(self::LOCATION_FIELD_NAME, T\GeoPointType::create())->build(),
        ]);

        MessageResolver::registerSchema($schema);
        return $schema;
    }

    /**
     * @return string
     */
    public function getTest1()
    {
        return $this->get(self::TEST1_FIELD_NAME);
    }

    /**
     * @param string $test1
     * @return self
     */
    public function setTest1($test1)
    {
        return $this->setSingleValue(self::TEST1_FIELD_NAME, $test1);
    }

    /**
     * @return array
     */
    public function getTest2()
    {
        return $this->get(self::TEST2_FIELD_NAME) ?: [];
    }

    /**
     * @param int $test2
     * @return self
     */
    public function addTest2($test2)
    {
        return $this->addToSet(self::TEST2_FIELD_NAME, [$test2]);
    }

    /**
     * @param int $test2
     * @return self
     */
    public function removeTest2($test2)
    {
        return $this->removeFromSet(self::TEST2_FIELD_NAME, [$test2]);
    }

    /**
     * @return GeoPoint
     */
    public function getLocation()
    {
        return $this->get(self::LOCATION_FIELD_NAME);
    }

    /**
     * @param GeoPoint $location
     * @return self
     */
    public function setLocation(GeoPoint $location)
    {
        return $this->setSingleValue(self::LOCATION_FIELD_NAME, $location);
    }
}