<?php

namespace Gdbots\Tests\Pbj\WellKnown;

use Gdbots\Pbj\WellKnown\DynamicField;
use Gdbots\Tests\Pbj\FixtureLoader;

class DynamicFieldTest extends \PHPUnit_Framework_TestCase
{
    use FixtureLoader;

    public function testAddToMessage()
    {
        $message = $this->createEmailMessage();
        $field = DynamicField::createFloatVal('float_val', 3.14);
        $message->addToList('dynamic_fields', [$field]);

        $this->assertTrue($message->getFromListAt('dynamic_fields', 2)->equals($field));

        echo json_encode($message, JSON_PRETTY_PRINT);
    }

    public function testCreateIntVal()
    {
        $field = DynamicField::createIntVal('test', 100);

        $this->assertSame('test', $field->getName());
        $this->assertSame(100, $field->getValue());
    }
}
