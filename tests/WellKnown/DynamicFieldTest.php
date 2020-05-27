<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj\WellKnown;

use Gdbots\Pbj\WellKnown\DynamicField;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;
use PHPUnit\Framework\TestCase;

class DynamicFieldTest extends TestCase
{
    public function testAddToMessage()
    {
        $message = EmailMessage::create();
        $field = DynamicField::createFloatVal('float_val', 3.14);
        $message->addToList('dynamic_fields', [$field]);

        $this->assertTrue($message->getFromListAt('dynamic_fields', 0)->equals($field));
    }

    public function testCreateBoolVal()
    {
        $field = DynamicField::createBoolVal('test', true);

        $this->assertSame('test', $field->getName());
        $this->assertSame(true, $field->getValue());
    }

    public function testCreateDateVal()
    {
        $field = DynamicField::createDateVal('test', new \DateTime('2015-12-25'));

        $this->assertSame('test', $field->getName());
        $this->assertSame((new \DateTime('2015-12-25'))->format('Ymd'), $field->getValue()->format('Ymd'));
    }

    public function testCreateFloatVal()
    {
        $field = DynamicField::createFloatVal('test', 3.14);

        $this->assertSame('test', $field->getName());
        $this->assertSame(3.14, $field->getValue());
    }

    public function testCreateIntVal()
    {
        $field = DynamicField::createIntVal('test', 100);

        $this->assertSame('test', $field->getName());
        $this->assertSame(100, $field->getValue());
    }

    public function testCreateStringVal()
    {
        $field = DynamicField::createStringVal('test', 'string');

        $this->assertSame('test', $field->getName());
        $this->assertSame('string', $field->getValue());
    }

    public function testCreateTextVal()
    {
        $field = DynamicField::createTextVal('test', 'string');

        $this->assertSame('test', $field->getName());
        $this->assertSame('string', $field->getValue());
    }
}
