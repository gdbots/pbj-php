<?php

namespace Gdbots\Tests\Pbj\Type;

use Gdbots\Pbj\FieldBuilder;
use Gdbots\Pbj\Type\TrinaryType;
use PHPUnit\Framework\TestCase;

class TrinaryTypeTest extends TestCase
{
    public function testEncode()
    {
        $field = FieldBuilder::create('trinary_unknown', TrinaryType::create())->build();
        $type = $field->getType();

        $this->assertSame(0, $type->encode(0, $field));
        $this->assertSame(1, $type->encode(1, $field));
        $this->assertSame(2, $type->encode(2, $field));
    }

    public function testDecode()
    {
        $field = FieldBuilder::create('trinary_unknown', TrinaryType::create())->build();
        $type = $field->getType();

        $this->assertSame(0, $type->decode(null, $field));
        $this->assertSame(0, $type->decode(0, $field));
        $this->assertSame(1, $type->decode(1, $field));
        $this->assertSame(2, $type->decode(2, $field));

        $this->assertSame(0, $type->decode('0', $field));
        $this->assertSame(1, $type->decode('1', $field));
        $this->assertSame(2, $type->decode('2', $field));
    }

    public function testValidValues()
    {
        $field = FieldBuilder::create('trinary_unknown', TrinaryType::create())->build();
        $type = $field->getType();

        $type->guard(0, $field);
        $type->guard(1, $field);
        $type->guard(2, $field);
        $this->assertTrue(true, 'Accepted valid values');
    }

    public function testInvalidValues()
    {
        $field = FieldBuilder::create('trinary_unknown', TrinaryType::create())->build();
        $type = $field->getType();

        $invalid = [
            'a',
            [],
            3,
            -1,
            false,
            true,
        ];

        foreach ($invalid as $val) {
            try {
                $type->guard($val, $field);
                $thrown = false;
            } catch (\Exception $e) {
                $thrown = true;
            }

            $this->assertTrue($thrown, 'Did not accept invalid value');

            if (!$thrown) {
                $this->fail(sprintf('TrinaryType field accepted invalid value [%s].', $val));
            }
        }
    }
}
