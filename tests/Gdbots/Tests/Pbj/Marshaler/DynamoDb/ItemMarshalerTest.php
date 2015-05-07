<?php

namespace Gdbots\Tests\Pbj\Marshaler\DynamoDb;

use Gdbots\Pbj\Marshaler\DynamoDb\ItemMarshaler;
use Gdbots\Tests\Pbj\FixtureLoader;

class ItemMarshalerTest extends \PHPUnit_Framework_TestCase
{
    use FixtureLoader;

    /** @var ItemMarshaler */
    protected $marshaler;

    public function setup()
    {
        $this->marshaler = new ItemMarshaler();
    }

    public function testMarshal()
    {
        $message = $this->createEmailMessage();
        $result = $this->marshaler->marshal($message);

        //echo json_encode($result, JSON_PRETTY_PRINT);
    }
}
