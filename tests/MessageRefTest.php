<?php

namespace Gdbots\Tests\Pbj;

use Gdbots\Pbj\MessageRef;
use PHPUnit\Framework\TestCase;

class MessageRefTest extends TestCase
{
    /**
     * @dataProvider getValidMessageRefs
     *
     * @param string $string
     */
    public function testEquals($string)
    {
        $ref1 = MessageRef::fromString($string);
        $ref2 = MessageRef::fromString($string);
        $this->assertTrue($ref1->equals($ref2));
        $this->assertTrue($ref2->equals($ref1));
    }

    /**
     * @dataProvider getValidMessageRefs
     *
     * @param string $string
     */
    public function testValidMessageRefs($string)
    {
        $this->assertSame($string, (string)MessageRef::fromString($string));
    }

    /**
     * @dataProvider getInvalidMessageRefs
     *
     * @param string $string
     */
    public function testInvalidMessageRefs($string)
    {
        try {
            $ref = MessageRef::fromString($string);
        } catch (\Exception $e) {
            $this->assertTrue(true, sprintf('MessageRef correctly failed on string [%s].', $string));
            return;
        }

        $this->fail(sprintf('MessageRef accepted and invalid string [%s].', $string));
    }

    /**
     * @return array
     */
    public function getValidMessageRefs()
    {
        return [
            ['acme:blog:node:article:123#tag'],
            ['acme:blog::article:123#tag'],
            ['acme:blog::article:123'],
            ['acme:blog:node:article:2015/12/25/test#tag'],
            ['acme:blog:node:article:2015/12/25/test'],
            ['acme:blog::article:2015/12/25/test#tag'],
            ['acme:blog::article:2015/12/25/test'],
        ];
    }

    /**
     * @return array
     */
    public function getInvalidMessageRefs()
    {
        return [
            ['test::what'],
            ['test::'],
            ['test:::'],
            [':test'],
            ['john@doe.com'],
            ['#hashtag'],
            ['http://www.what.com/'],
            ['test.value:2015/01/01/test:what'],
            ['cool~topic'],
            ['some:thin!@##$%$%&^^&**()-=+'],
            ['some:test%20'],
            ['ACME:blog:node:article:1:2:3:4#tag'],
            ['ACME:blog:node:article#tag'],
            ['ACME:blog:node:'],
            ['ACME:blog::'],
            ['ACME:::'],
            ['acme:blog:node:'],
            ['acme:blog::'],
            ['acme:::'],
            ['acme:::#tag'],
        ];
    }
}
