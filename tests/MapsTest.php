<?php

namespace Gdbots\Tests\Pbj;

use Gdbots\Tests\Pbj\Fixtures\MapsMessage;

class MapsTest extends \PHPUnit_Framework_TestCase
{
    public function testStringMap()
    {
        $message = MapsMessage::create()
            ->addToMap('String', 'test1', '123')
            ->addToMap('String', 'test2', '456');
        $this->assertSame($message->get('String'), ['test1' => '123', 'test2' => '456']);

        $message->removeFromMap('String', 'test2');
        $this->assertSame($message->get('String'), ['test1' => '123']);

        $message
            ->addToMap('String', 'test2', '456')
            ->addToMap('String', 'test3', '789');
        $this->assertSame($message->get('String'), ['test1' => '123', 'test2' => '456', 'test3' => '789']);

        //echo json_encode($message->schema(), JSON_PRETTY_PRINT);
        //echo json_encode($message, JSON_PRETTY_PRINT);
    }
}
