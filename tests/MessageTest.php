<?php

namespace Gdbots\Tests\Pbj;

use Gdbots\Common\Enum;
use Gdbots\Pbj\Exception\FrozenMessageIsImmutable;
use Gdbots\Tests\Pbj\Fixtures\Enum\Priority;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;
use Gdbots\Tests\Pbj\Fixtures\Enum\Provider;
use Gdbots\Tests\Pbj\Fixtures\MapsMessage;
use Gdbots\Tests\Pbj\Fixtures\NestedMessage;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    use FixtureLoader;

    public function testCreateMessageFromArray()
    {
        /** @var EmailMessage $message */
        $message = $this->createEmailMessage();
        $message->set('priority', Priority::HIGH());

        $this->assertTrue($message->get('priority')->equals(Priority::HIGH));
        $this->assertTrue(Priority::HIGH() === $message->get('priority'));

        $json = $this->getSerializer()->serialize($message);
        $message = $this->getSerializer()->deserialize($json);

        $this->assertTrue($message->get('priority')->equals(Priority::HIGH));
        $this->assertTrue(Priority::HIGH() === $message->get('priority'));
        $this->assertSame($message->get('nested')->get('location')->getLatitude(), 0.5);

        //echo json_encode($message, JSON_PRETTY_PRINT);
        //echo json_encode($message->schema(), JSON_PRETTY_PRINT);
        //echo json_encode($message->schema()->getMixins(), JSON_PRETTY_PRINT);
    }

    public function testUniqueItemsInSet()
    {
        $message = EmailMessage::create()
            ->addToSet('labels', ['CHICKEN', 'Chicken', 'chicken', 'DONUTS', 'Donuts', 'donuts'])
        ;

        $this->assertCount(2, $message->get('labels'));
        $this->assertSame($message->get('labels'), ['chicken', 'donuts']);
    }

    public function testisInSet()
    {
        $message = EmailMessage::create()
            ->addToSet('labels', ['abc'])
            ->addToSet(
                'enum_in_set',
                [
                    Provider::AOL(),
                    Provider::GMAIL(),
                ]
            );

        $this->assertTrue($message->isInSet('labels', 'abc'));
        $this->assertFalse($message->isInSet('labels', 'idontexist'));
        $this->assertTrue($message->isInSet('enum_in_set', Provider::AOL()));
        $this->assertFalse($message->isInSet('enum_in_set', Provider::HOTMAIL()));
    }

    public function testEnumInSet()
    {
        $message = EmailMessage::create()
            ->addToSet(
                'enum_in_set',
                [
                    Provider::AOL(),
                    Provider::AOL(),
                    Provider::GMAIL(),
                    Provider::GMAIL(),
                ]
            );

        $result = array_map(
            function (Enum $enum) {
                return $enum->getValue();
            },
            $message->get('enum_in_set') ?: []
        );

        $this->assertCount(2, $result);
        $this->assertSame($result, ['aol', 'gmail']);
    }

    public function testisInList()
    {
        $message = $this->createEmailMessage();

        /** @var MapsMessage $messageInList */
        $messageInList = $message->get('any_of_message')[0];
        $messageNotInList = clone $messageInList;
        $messageNotInList->addToMap('String', 'key', 'val');

        $this->assertTrue($message->isInList('any_of_message', $messageInList));
        $this->assertFalse($message->isInList('any_of_message', $messageNotInList));
        $this->assertFalse($message->isInList('any_of_message', 'notinlist'));
        $this->assertFalse($message->isInList('any_of_message', NestedMessage::create()));
        $this->assertTrue($message->isInList('enum_in_list', 'aol'));
        $this->assertTrue($message->isInList('enum_in_list', Provider::AOL()));
        $this->assertFalse($message->isInList('enum_in_list', 'notinlist'));
        $this->assertFalse($message->isInList('enum_in_list', Provider::HOTMAIL()));
    }

    public function testEnumInList()
    {
        $message = EmailMessage::create()
            ->addToList(
                'enum_in_list',
                [
                    Provider::AOL(),
                    Provider::AOL(),
                    Provider::GMAIL(),
                    Provider::GMAIL(),
                ]
            );

        $result = array_map(
            function (Enum $enum) {
                return $enum->getValue();
            },
            $message->get('enum_in_list')
        );

        $this->assertCount(4, $result);
        $this->assertSame($result, ['aol', 'aol', 'gmail', 'gmail']);
    }

    public function testisInMap()
    {
        $message = MapsMessage::create();
        $message->addToMap('String', 'string1', 'val1');

        $this->assertTrue($message->isInMap('String', 'string1'));
        $this->assertFalse($message->isInMap('String', 'notinmap'));
        $this->assertFalse($message->isInMap('Microtime', 'notinmap'));

        $message->clear('String');
        $this->assertFalse($message->isInMap('String', 'string1'));
    }

    public function testNestedMessage()
    {
        $message = $this->createEmailMessage();
        $nestedMessage = NestedMessage::create()
            ->set('test1', 'val1')
            ->addToSet('test2', [1, 2])
        ;

        $message->set('nested', $nestedMessage);
        $this->assertSame($nestedMessage->get('test2'), [1, 2]);
        $this->assertSame($message->get('nested'), $nestedMessage);
    }

    public function testAnyOfMessageInList()
    {
        $message = EmailMessage::create()
            ->addToList(
                'any_of_message',
                [
                    MapsMessage::create()->addToMap('String', 'test:field:name', 'value1'),
                    NestedMessage::create()->set('test1', 'value1')
                ]
        );

        $this->assertCount(2, $message->get('any_of_message'));
    }

    public function testFreeze()
    {
        $message = $this->createEmailMessage();
        $nestedMessage = NestedMessage::create();
        $message->set('nested', $nestedMessage);

        $message->freeze();
        $this->assertTrue($message->isFrozen());
        $this->assertTrue($nestedMessage->isFrozen());
    }

    /**
     * @expectedException \Gdbots\Pbj\Exception\FrozenMessageIsImmutable
     */
    public function testFrozenMessageIsImmutable()
    {
        $message = $this->createEmailMessage();
        $nestedMessage = NestedMessage::create();
        $message->set('nested', $nestedMessage);

        $message->freeze();
        $message->set('from_name', 'homer');
        $nestedMessage->set('test1', 'test1');
    }

    public function testClone()
    {
        $message = $this->createEmailMessage();
        $nestedMessage = NestedMessage::create();
        $message->set('nested', $nestedMessage);

        $nestedMessage->set('test1', 'original');
        $message2 = clone $message;
        $message2->set('from_name', 'marge')->get('nested')->set('test1', 'clone');

        $this->assertNotSame($message, $message2);
        $this->assertNotSame($message->get('date_sent'), $message2->get('date_sent'));
        $this->assertNotSame($message->get('microtime_sent'), $message2->get('microtime_sent'));
        $this->assertNotSame($message->get('nested'), $message2->get('nested'));
        $this->assertNotSame($message->get('nested')->get('test1'), $message2->get('nested')->get('test1'));
    }

    public function testCloneIsMutableAfterOriginalIsFrozen()
    {
        $message = $this->createEmailMessage();
        $nestedMessage = NestedMessage::create();
        $message->set('nested', $nestedMessage);

        $nestedMessage->set('test1', 'original');
        $message->freeze();

        $message2 = clone $message;
        $message2->set('from_name', 'marge')->get('nested')->set('test1', 'clone');

        try {
            $message->set('from_name', 'homer')->get('nested')->set('test1', 'original');
            $this->fail('Original message should still be immutable.');
        } catch (FrozenMessageIsImmutable $e) {
        }
    }
}
