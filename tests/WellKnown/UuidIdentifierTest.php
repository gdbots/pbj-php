<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj\WellKnown;

use Gdbots\Pbj\WellKnown\UuidIdentifier;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\Uuid;

class UuidIdentifierTest extends TestCase
{
    public function testGenerate()
    {
        $id = UuidIdentifier::generate();
        $this->assertTrue(Uuid::isValid((string)$id));

        $uuid = Uuid::fromString($id->toString());
        $this->assertInstanceOf(UuidV4::class, $uuid);
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
