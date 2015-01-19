<?php

namespace Gdbots\Tests\Pbj;

use Gdbots\Tests\Pbj\Fixtures\MapsMessage;

class MapsTest extends \PHPUnit_Framework_TestCase
{
    public function testStringMap()
    {
        $message = MapsMessage::create()
            ->addToAMap('String', 'test1', '123')
            ->addToAMap('String', 'test2', '456');
        $this->assertSame($message->getAMap('String'), ['test1' => '123', 'test2' => '456']);

        $message->removeFromAMap('String', 'test2');
        $this->assertSame($message->getAMap('String'), ['test1' => '123']);

        $message
            ->addToAMap('String', 'test2', '456')
            ->addToAMap('String', 'test3', '789');
        $this->assertSame($message->getAMap('String'), ['test1' => '123', 'test2' => '456', 'test3' => '789']);
    }
}
