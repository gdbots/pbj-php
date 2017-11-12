<?php

namespace Gdbots\Tests\Pbj\WellKnown;

use Gdbots\Pbj\WellKnown\DatedSlugIdentifier;
use PHPUnit\Framework\TestCase;

class SampleDatedSlugIdentifier extends DatedSlugIdentifier
{
}

class DatedSlugIdentifierTest extends TestCase
{
    public function testCreate()
    {
        $date = new \DateTime();
        $datePart = $date->format('Y/m/d');

        $expected = [
            "$datePart/homer-simpson" => 'Homer Simpson!',
            "$datePart/marge-simpson" => '--Marge __ Simpson!--',
            "2012/12/12/bart-simpson" => '-/2012/12/12/--Bart __ Simpson!--/',
        ];
        foreach ($expected as $k => $v) {
            $this->assertSame($k, SampleDatedSlugIdentifier::create($v)->toString());
        }
    }

    public function testFromString()
    {
        $expected = '2012/12/12/valid-slug';
        $slug = SampleDatedSlugIdentifier::fromString($expected);
        $this->assertSame($expected, $slug->toString());
    }

    public function testEquals()
    {
        $expected = '2012/12/12/valid-slug';
        $slug = SampleDatedSlugIdentifier::fromString($expected);
        $slug2 = SampleDatedSlugIdentifier::fromString($slug->toString());
        $this->assertTrue($slug->equals($slug2));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFromStringInvalid()
    {
        $slug = SampleDatedSlugIdentifier::fromString('invalid-slug');
    }
}
