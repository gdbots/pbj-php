<?php

namespace Gdbots\Tests\Pbj;

use Gdbots\Tests\Pbj\Fixtures\Enum\Priority;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return EmailMessage
     */
    private function createEmailMessage()
    {
        $json = <<<JSON
{
    "from_name": "homer  ",
    "from_email": "homer@thesimpsons.com",
    "priority": 2,
    "sent": false,
    "date_sent": "2014-12-25",
    "provider": "gmail",
    "labels": [
        "donuts",
        "mmmm",
        "chicken"
    ]
}
JSON;
        return EmailMessage::fromArray(json_decode($json, true));
    }

    public function testCreateMessageFromArray()
    {
        $message = $this->createEmailMessage();
        $message->setPriority(Priority::HIGH());

        $this->assertTrue($message->getPriority()->equals(Priority::HIGH));
        $this->assertTrue(Priority::HIGH() === $message->getPriority());

        $json = json_encode($message);
        $message = EmailMessage::fromArray(json_decode($json, true));

        $this->assertTrue($message->getPriority()->equals(Priority::HIGH));
        $this->assertTrue(Priority::HIGH() === $message->getPriority());

        //echo json_encode($message, JSON_PRETTY_PRINT);
        //echo json_encode($message->schema(), JSON_PRETTY_PRINT);
    }

    public function testUniqueItemsInSet()
    {
        $message = $this->createEmailMessage();
        $message
            ->addLabel('CHICKEN')
            ->addLabel('DoNUTS')
            ->addLabel('chicKen');

        $this->assertCount(3, $message->getLabels());
        $this->assertSame($message->getLabels(), ['DoNUTS', 'mmmm', 'chicKen']);
    }
}