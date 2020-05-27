<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj\WellKnown;

use Gdbots\Pbj\Exception\AssertionFailed;
use Gdbots\Pbj\Exception\InvalidSchemaQName;
use Gdbots\Pbj\SchemaQName;
use Gdbots\Pbj\WellKnown\MessageRef;
use Gdbots\Pbj\WellKnown\NodeRef;
use PHPUnit\Framework\TestCase;

class NodeRefTest extends TestCase
{
    public function testEquals()
    {
        $nodeRef1 = NodeRef::fromString('acme:article:123');
        $nodeRef2 = NodeRef::fromString('acme:article:123');
        $this->assertTrue($nodeRef1->equals($nodeRef2));
        $this->assertTrue($nodeRef2->equals($nodeRef1));
    }

    public function testNotEquals()
    {
        $nodeRef1 = NodeRef::fromString('acme:article:123');
        $nodeRef2 = NodeRef::fromString('acme:article:1234');
        $this->assertFalse($nodeRef1->equals($nodeRef2));
        $this->assertFalse($nodeRef2->equals($nodeRef1));
    }

    public function testValidQname()
    {
        $nodeRef = NodeRef::fromString('acme:article:123');
        $this->assertSame('acme', $nodeRef->getVendor());

        $nodeRef = NodeRef::fromString('acme-widgets:article:123');
        $this->assertSame('acme-widgets', $nodeRef->getVendor());
    }

    public function testInvalidQname()
    {
        $this->expectException(InvalidSchemaQName::class);
        NodeRef::fromString('ACME:article:123');
    }

    public function testEmptyFromString()
    {
        $this->expectException(AssertionFailed::class);
        NodeRef::fromString('');
    }

    public function testInvalidId()
    {
        $this->expectException(AssertionFailed::class);
        NodeRef::fromString('acme:article:invalid!#id');
    }

    public function testMissingId()
    {
        $this->expectException(AssertionFailed::class);
        NodeRef::fromString('acme:article');
    }

    public function testComplicatedId()
    {
        $nodeRef = NodeRef::fromString('acme:article:this/id/is:WeirD:and:_.what');
        $this->assertSame('acme', $nodeRef->getVendor());
        $this->assertSame('article', $nodeRef->getLabel());
        $this->assertSame('this/id/is:WeirD:and:_.what', $nodeRef->getId());
    }

    public function testGetProperties()
    {
        $nodeRef = NodeRef::fromString('acme:article:123');
        $this->assertSame('acme', $nodeRef->getVendor());
        $this->assertSame('article', $nodeRef->getLabel());
        $this->assertSame('123', $nodeRef->getId());
        $this->assertSame(SchemaQName::fromString('acme:article'), $nodeRef->getQName());
    }

    public function testToString()
    {
        $expected = 'acme:article:this/id/is:WeirD:and:_.what';
        $nodeRef = NodeRef::fromString($expected);
        $this->assertSame($expected, $nodeRef->toString());
    }

    public function testClone()
    {
        $expected = 'acme:article:this/id/is:WeirD:and:_.what';
        $nodeRef1 = NodeRef::fromString($expected);
        $nodeRef2 = clone $nodeRef1;

        $this->assertSame($expected, $nodeRef1->toString());
        $this->assertSame($expected, $nodeRef2->toString());

        $this->assertTrue($nodeRef1->equals($nodeRef2));
        $this->assertTrue($nodeRef2->equals($nodeRef1));
    }

    public function testFromMessageRef()
    {
        $nodeRef = NodeRef::fromMessageRef(MessageRef::fromString('acme:blog:node:article:123'));
        $this->assertSame('acme', $nodeRef->getVendor());
        $this->assertSame('article', $nodeRef->getLabel());
        $this->assertSame('123', $nodeRef->getId());
    }

    public function testToFilePath()
    {
        $nodeRef = NodeRef::fromString('acme:article:123');
        $this->assertSame('acme/article/20/2c/123', $nodeRef->toFilePath());
        $this->assertTrue($nodeRef->equals(NodeRef::fromFilePath($nodeRef->toFilePath())));
        $this->assertSame($nodeRef->toString(), NodeRef::fromFilePath($nodeRef->toFilePath())->toString());

        $nodeRef = NodeRef::fromString('acme:article:2015/12/25/test');
        $this->assertSame('acme/article/d9/20/2015__FS__12__FS__25__FS__test', $nodeRef->toFilePath());
        $this->assertTrue($nodeRef->equals(NodeRef::fromFilePath($nodeRef->toFilePath())));
        $this->assertSame($nodeRef->toString(), NodeRef::fromFilePath($nodeRef->toFilePath())->toString());

        $nodeRef = NodeRef::fromString('acme-widgets:poll-widget:a:b:C_');
        $this->assertSame('acme-widgets/poll-widget/69/a9/a__CLN__b__CLN__C_', $nodeRef->toFilePath());
        $this->assertTrue($nodeRef->equals(NodeRef::fromFilePath($nodeRef->toFilePath())));
        $this->assertSame($nodeRef->toString(), NodeRef::fromFilePath($nodeRef->toFilePath())->toString());
    }
}
