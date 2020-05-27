<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj;

use Gdbots\Pbj\Enum;
use PHPUnit\Framework\TestCase;

/**
 * @method static TestEnum CONST1()
 * @method static TestEnum CONST2()
 */
class TestEnum extends Enum
{
    const CONST1 = 'val1';
    const CONST2 = 'val2';
}

class EnumTest extends TestCase
{
    public function testCreate()
    {
        $enum = TestEnum::CONST1();
        $this->assertInstanceOf(Enum::class, $enum);
        $this->assertInstanceOf(TestEnum::class, $enum);
    }

    public function testValues()
    {
        $enum = TestEnum::CONST1();
        $this->assertSame($enum->values(), ['CONST1' => 'val1', 'CONST2' => 'val2']);
        $this->assertSame($enum->getValue(), 'val1');
        $this->assertSame($enum->getName(), 'CONST1');
    }

    public function testWrongValue()
    {
        try {
            TestEnum::create('notvalid');
            $this->fail('Enum allowed creation with invalid constant.');
        } catch (\Throwable $e) {
            $this->assertTrue(true);
        }
    }

    public function testEquals()
    {
        $enum1 = TestEnum::CONST1();
        $enum2 = TestEnum::CONST1();
        $this->assertSame($enum1, $enum2);
        $this->assertTrue($enum1->equals($enum2));
    }

    public function testKeys()
    {
        $this->assertSame(TestEnum::CONST1()->keys(), ['CONST1', 'CONST2']);
    }
}
