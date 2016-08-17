<?php

namespace Gdbots\Tests\Pbj\WellKnown;

use Gdbots\Pbj\WellKnown\SlugIdentifier;

class SampleSlugIdentifier extends SlugIdentifier {}

class SlugIdentifierTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFromStringInvalid()
    {
        $slug = SampleSlugIdentifier::fromString('!invalid Slug');
    }
}
