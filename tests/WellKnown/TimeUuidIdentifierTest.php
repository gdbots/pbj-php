<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj\WellKnown;

use Gdbots\Pbj\WellKnown\TimeUuidIdentifier;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Rfc4122\UuidV1;
use Ramsey\Uuid\Uuid;

class TimeUuidIdentifierTest extends TestCase
{
    public function testGenerate()
    {
        $id = TimeUuidIdentifier::generate();
        $this->assertTrue(Uuid::isValid((string)$id));

        $uuid = Uuid::fromString($id->toString());
        $this->assertTrue($uuid instanceof UuidV1);
    }

    public function testFromString()
    {
        $id = TimeUuidIdentifier::fromString(Uuid::NAMESPACE_DNS);
        $this->assertSame($id->toString(), Uuid::NAMESPACE_DNS);
    }

    public function testEquals()
    {
        $id = TimeUuidIdentifier::fromString(Uuid::NAMESPACE_DNS);
        $id2 = TimeUuidIdentifier::fromString(Uuid::NAMESPACE_DNS);
        $id3 = TimeUuidIdentifier::fromString(Uuid::NAMESPACE_OID);
        $this->assertTrue($id->equals($id2));
        $this->assertFalse($id->equals($id3));
    }
}
