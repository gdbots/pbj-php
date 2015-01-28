<?php

namespace Gdbots\Tests\Pbj;

use Gdbots\Pbj\Serializer\JsonSerializer;
use Gdbots\Pbj\Serializer\Serializer;
use Gdbots\Tests\Pbj\Fixtures\Enum\Priority;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;
use Gdbots\Tests\Pbj\Fixtures\NestedMessage;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    /** @var Serializer */
    protected $serializer;

    /**
     * @return Serializer
     */
    protected function getSerializer()
    {
        if (null === $this->serializer) {
            $this->serializer = new JsonSerializer();
        }
        return $this->serializer;
    }

    /**
     * @return EmailMessage
     */
    private function createEmailMessage()
    {
        $json = file_get_contents(__DIR__ . '/Fixtures/email-message.json');
        // auto registers the schema with the MessageResolver
        // only done for tests or dynamic messages.
        EmailMessage::schema();
        return $this->getSerializer()->deserialize($json);
    }

    public function testCreateMessageFromArray()
    {
        /** @var EmailMessage $message */
        $message = $this->createEmailMessage();
        $message->setPriority(Priority::HIGH());

        $this->assertTrue($message->getPriority()->equals(Priority::HIGH));
        $this->assertTrue(Priority::HIGH() === $message->getPriority());

        $json = $this->getSerializer()->serialize($message);
        $message = $this->getSerializer()->deserialize($json);

        $this->assertTrue($message->getPriority()->equals(Priority::HIGH));
        $this->assertTrue(Priority::HIGH() === $message->getPriority());

        //echo json_encode($message, JSON_PRETTY_PRINT);
        //echo json_encode($message->schema(), JSON_PRETTY_PRINT);
    }

    public function testUniqueItemsInSet()
    {
        $message = EmailMessage::create()
            ->addLabel('CHICKEN')
            ->addLabel('Chicken')
            ->addLabel('chicken')
            ->addLabel('DONUTS')
            ->addLabel('Donuts')
            ->addLabel('donuts')
        ;

        $this->assertCount(2, $message->getLabels());
        $this->assertSame($message->getLabels(), ['chicken', 'donuts']);
    }

    public function testNestedMessage()
    {
        $message = $this->createEmailMessage();
        $nestedMessage = NestedMessage::create()
            ->setTest1('val1')
            ->addTest2(1)
            ->addTest2(2)
        ;

        $message->setNested($nestedMessage);
        $this->assertSame($nestedMessage->getTest2(), [1, 2]);
        $this->assertSame($message->getNested(), $nestedMessage);
    }
}