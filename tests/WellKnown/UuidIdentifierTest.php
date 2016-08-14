<?php

namespace Gdbots\Tests\Pbj\WellKnown;

use Gdbots\Pbj\WellKnown\UuidIdentifier;
use Ramsey\Uuid\Uuid;

class UuidIdentifierTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $id = UuidIdentifier::generate();
        $this->assertTrue(Uuid::isValid($id));

        $uuid = Uuid::fromString($id->toString());
        $this->assertTrue($uuid->getVersion() == 4);
    }

    public function testFromString()
    {
        $id = UuidIdentifier::fromString(Uuid::NAMESPACE_DNS);
        $this->assertSame($id->toString(), Uuid::NAMESPACE_DNS);
    }

    public function testEquals()
    {
        $id = UuidIdentifier::fromString(Uuid::NAMESPACE_DNS);
        $id2 = UuidIdentifier::fromString(Uuid::NAMESPACE_DNS);
        $id3 = UuidIdentifier::fromString(Uuid::NAMESPACE_OID);
        $this->assertTrue($id->equals($id2));
        $this->assertFalse($id->equals($id3));
    }
}
