<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj\WellKnown;

use Gdbots\Pbj\WellKnown\SlugIdentifier;
use PHPUnit\Framework\TestCase;

class SampleSlugIdentifier extends SlugIdentifier
{
}

class SlugIdentifierTest extends TestCase
{
    public function testCreate()
    {
        $expected = [
            'homer-simpson' => 'Homer Simpson!',
            'marge-simpson' => '--Marge __ Simpson!--',
        ];
        foreach ($expected as $k => $v) {
            $this->assertSame($k, SampleSlugIdentifier::create($v)->toString());
        }
    }

    public function testFromString()
    {
        $expected = 'valid-slug';
        $slug = SampleSlugIdentifier::fromString($expected);
        $this->assertSame($expected, $slug->toString());
    }

    public function testEquals()
    {
        $expected = 'valid-slug';
        $slug = SampleSlugIdentifier::fromString($expected);
        $slug2 = SampleSlugIdentifier::fromString($slug->toString());
        $this->assertTrue($slug->equals($slug2));
    }

    public function testFromStringInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        SampleSlugIdentifier::fromString('!invalid Slug');
    }
}
