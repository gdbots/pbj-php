<?php

namespace Gdbots\Tests\Pbj\WellKnown;

use Gdbots\Pbj\WellKnown\DynamicField;

class DynamicFieldTest extends \PHPUnit_Framework_TestCase
{
    public function testIntField()
    {
        $field = DynamicField::createInt('test', 100);

        $this->assertSame('test', $field->getName());
        $this->assertSame(100, $field->getValue());
    }
}
