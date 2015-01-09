<?php

namespace Gdbots\Tests\Messages;

use Gdbots\Tests\Messages\Enum\Priority;
use Gdbots\Tests\Messages\Enum\Provider;

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
        $i = 0;
        do {
            $i++;
            $message = $this->createEmailMessage();
            $message->setPriority(Priority::HIGH());

            $this->assertTrue($message->getPriority()->equals(Priority::HIGH));
            $this->assertTrue(Priority::HIGH() === $message->getPriority());

            $json = json_encode($message, JSON_PRETTY_PRINT) . PHP_EOL;
            $arr = json_decode($json, true);

            $message2 = EmailMessage::fromArray($arr);
            $message2
                ->markAsSent()
                ->setPriority(Priority::LOW())
                ->setProvider(Provider::AOL());
        } while ($i < 1);

        echo json_encode($message2, JSON_PRETTY_PRINT) . PHP_EOL;
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