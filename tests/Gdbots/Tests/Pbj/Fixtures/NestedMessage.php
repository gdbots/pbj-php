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
    const TEST1 = 'test1';
    const TEST2 = 'test2';
    const LOCATION = 'location';

    /**
     * @return Schema
     */
    protected static function defineSchema()
    {
        $schema = Schema::create(__CLASS__, 'pbj:gdbots:tests.pbj:fixtures:nested-message:1-0-0', [
            Fb::create(self::TEST1, T\StringType::create())->build(),
            Fb::create(self::TEST2, T\IntType::create())->asASet()->build(),
            Fb::create(self::LOCATION, T\GeoPointType::create())->build(),
        ]);

        MessageResolver::registerSchema($schema);
        return $schema;
    }

    /**
     * @return string
     */
    public function getTest1()
    {
        return $this->get(self::TEST1);
    }

    /**
     * @param string $test1
     * @return self
     */
    public function setTest1($test1)
    {
        return $this->setSingleValue(self::TEST1, $test1);
    }

    /**
     * @return array
     */
    public function getTest2()
    {
        return $this->get(self::TEST2) ?: [];
    }

    /**
     * @param int $test2
     * @return self
     */
    public function addTest2($test2)
    {
        return $this->addToSet(self::TEST2, [$test2]);
    }

    /**
     * @param int $test2
     * @return self
     */
    public function removeTest2($test2)
    {
        return $this->removeFromSet(self::TEST2, [$test2]);
    }

    /**
     * @return GeoPoint
     */
    public function getLocation()
    {
        return $this->get(self::LOCATION);
    }

    /**
     * @param GeoPoint $location
     * @return self
     */
    public function setLocation(GeoPoint $location)
    {
        return $this->setSingleValue(self::LOCATION, $location);
    }
}