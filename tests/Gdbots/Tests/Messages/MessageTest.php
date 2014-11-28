<?php

namespace Gdbots\Tests\Messages;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateMessageFromArray()
    {
        $firstName = 'Joe  ';
        $message = ExampleMessage::fromArray([
                ExampleMessage::FIRST_NAME => $firstName,
            ]);

        $message->setAnInt(500)->setLastName(null);
        $this->assertTrue($firstName === $message->getFirstName());

        echo json_encode($message, JSON_PRETTY_PRINT) . PHP_EOL;
        echo serialize($message);
    }
}