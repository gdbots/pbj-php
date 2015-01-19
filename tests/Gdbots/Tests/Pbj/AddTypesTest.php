<?php

namespace Gdbots\Tests\Pbj;

use Gdbots\Common\BigNumber;
use Gdbots\Common\Util\StringUtils;
use Gdbots\Tests\Pbj\Fixtures\Enum\IntEnum;
use Gdbots\Tests\Pbj\Fixtures\Enum\Priority;
use Gdbots\Tests\Pbj\Fixtures\Enum\Provider;
use Gdbots\Tests\Pbj\Fixtures\Enum\StringEnum;
use Gdbots\Tests\Pbj\Fixtures\MapsMessage;

class AddTypesTest extends \PHPUnit_Framework_TestCase
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

    protected function getInvalidTypeValues()
    {
        return [
            'BigInt'          => [new BigNumber(-1), new BigNumber('18446744073709551616')],
            'Boolean'         => 'not_a_bool',
            'Date'            => 'not_a_date',
            'Decimal'         => 1,
            'Float'           => 1,
            'IntEnum'         => Priority::NORMAL(), // not the correct enum
            'Int'             => [-1, 4294967296],
            'MediumInt'       => [-1, 16777216],
            'SignedBigInt'    => [new BigNumber('-9223372036854775809'), new BigNumber('9223372036854775808')],
            'SignedMediumInt' => [-8388609, 8388608],
            'SignedSmallInt'  => [-32769, 32768],
            'SignedTinyInt'   => [-129, 128],
            'SmallInt'        => [-1, 65536],
            'StringEnum'      => Provider::AOL(), // not the correct enum
            'String'          => false,
            'TinyInt'         => [-1, 256],
        ];
    }

    public function testAddInvalidTypes()
    {
        $message = MapsMessage::create();

        foreach ($this->getInvalidTypeValues() as $k => $v) {
            $thrown = false;
            try {
                if (is_array($v)) {
                    $message->addToAMap($k, 'test1', $v[0]);
                    $message->addToAMap($k, 'test2', $v[1]);
                } else {
                    $message->addToAMap($k, 'test1', $v);
                }
            } catch (\Exception $e) {
                $thrown = true;
            }

            if (!$thrown) {
                if (is_array($v)) {
                    $this->fail(sprintf('[%s] accepted an invalid [%s] value', $k, StringUtils::varToString($v[0])));
                    $this->fail(sprintf('[%s] accepted an invalid [%s] value', $k, StringUtils::varToString($v[1])));
                } else {
                    $this->fail(sprintf('[%s] accepted an invalid [%s] value', $k, StringUtils::varToString($v)));
                }
            }
        }
    }

    public function testAddInvalidTypeToMap()
    {
        $shouldWork = MapsMessage::create();
        $shouldFail = clone $shouldWork;

        /*
         * some int types won't fail because they're all ints of course, just different ranges.
         * e.g. an Int is also all other unsigned ints (except BigInt but that's a class so we're fine)
         */
        $allInts = ['TinyInt', 'SmallInt', 'MediumInt', 'Int', 'SignedTinyInt', 'SignedSmallInt', 'SignedMediumInt', 'SignedInt'];

        foreach ($shouldWork::getAllTypes() as $type => $class) {
            foreach ($this->getTypeValues() as $k => $v) {
                $thrown = false;
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

                    if ('Decimal' == $type && 'Float' == $k || 'Float' == $type && 'Decimal' == $k) {
                        continue;
                    } elseif (false !== strpos($type, 'Int') && in_array($k, $allInts)) {
                        continue;
                     }
                } catch (\Exception $e) {
                    $thrown = true;
                }

                if (!$thrown) {
                    if (is_array($v)) {
                        $this->fail(sprintf('[%s] accepted an invalid/mismatched [%s] value', $type, StringUtils::varToString($v[0])));
                        $this->fail(sprintf('[%s] accepted an invalid/mismatched [%s] value', $type, StringUtils::varToString($v[1])));
                    } else {
                        $this->fail(sprintf('[%s] accepted an invalid/mismatched [%s] value', $type, StringUtils::varToString($v)));
                    }
                }
            }
        }
    }
}