<?php

namespace Gdbots\Tests\Messages;

use Gdbots\Tests\Messages\Enum\IntEnum;
use Gdbots\Tests\Messages\Enum\StringEnum;
use Moontoast\Math\BigNumber;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    protected function getTypeValues()
    {
        return [
            'BigInt'          => [new BigNumber(0), new BigNumber('18446744073709551615')],
            'Boolean'         => [false, true],
            'Date'            => new \DateTime(),
            'Decimal'         => 3.14,
            'Float'           => 13213.032468,
            'IntEnum'         => IntEnum::UNKNOWN(),
            'Int'             => [0, 4294967295],
            'MediumInt'       => [0, 16777215],
            'SignedBigInt'    => [new BigNumber('-9223372036854775808'), new BigNumber('9223372036854775807')],
            'SignedMediumInt' => [-8388608, 8388607],
            'SignedSmallInt'  => [-32768, 32767],
            'SignedTinyInt'   => [-128, 127],
            'SmallInt'        => [0, 65535],
            'StringEnum'      => StringEnum::UNKNOWN(),
            'String'          => 'string',
            'TinyInt'         => [0, 255],
        ];
    }

    public function testMap()
    {
        $message = MapsMessage::fromArray()
            ->addToAMap('String', 'test1', '123')
            ->addToAMap('String', 'test2', '456');

        $this->assertSame($message->getAMap('String'), ['test1' => '123', 'test2' => '456']);

        $message->removeFromAMap('String', 'test2');
        $this->assertSame($message->getAMap('String'), ['test1' => '123']);

        $message->addToAMap('String', 'test2', '456');
        $this->assertSame($message->getAMap('String'), ['test1' => '123', 'test2' => '456']);
    }

    /**
     * expectedException \Assert\InvalidArgumentException
     */
    public function testAddInvalidTypeToMap()
    {
        $shouldWork = MapsMessage::fromArray();
        $shouldFail = clone $shouldWork;

        foreach ($shouldWork::getAllTypes() as $type => $class) {
            foreach ($this->getTypeValues() as $k => $v) {
                if ($type == $k) {
                    if (is_array($v)) {
                        $shouldWork->addToAMap($type, 'test1', $v[0]);
                        $shouldWork->addToAMap($type, 'test2', $v[1]);
                    } else {
                        $shouldWork->addToAMap($type, 'test1', $v);
                    }
                    continue;
                }

                try {
                    if (is_array($v)) {
                        $shouldFail->addToAMap($type, 'test1', $v[0]);
                        $shouldFail->addToAMap($type, 'test2', $v[1]);
                    } else {
                        $shouldFail->addToAMap($type, 'test1', $v);
                    }

                    $this->fail(sprintf('[%s] map accepted a [%s] value', $type, $k));
                } catch (\Exception $e) {
                }
            }
        }

        //echo json_encode($shouldWork, JSON_PRETTY_PRINT) . PHP_EOL;
    }
}