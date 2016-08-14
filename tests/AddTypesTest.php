<?php

namespace Gdbots\Tests\Pbj;

use Gdbots\Common\Util\StringUtils;
use Gdbots\Pbj\MessageRef;
use Gdbots\Pbj\WellKnown\BigNumber;
use Gdbots\Pbj\WellKnown\DynamicField;
use Gdbots\Pbj\WellKnown\GeoPoint;
use Gdbots\Pbj\WellKnown\Microtime;
use Gdbots\Pbj\WellKnown\TimeUuidIdentifier;
use Gdbots\Pbj\WellKnown\UuidIdentifier;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;
use Gdbots\Tests\Pbj\Fixtures\Enum\IntEnum;
use Gdbots\Tests\Pbj\Fixtures\Enum\Priority;
use Gdbots\Tests\Pbj\Fixtures\Enum\Provider;
use Gdbots\Tests\Pbj\Fixtures\Enum\StringEnum;
use Gdbots\Tests\Pbj\Fixtures\MapsMessage;
use Gdbots\Tests\Pbj\Fixtures\NestedMessage;

class AddTypesTest extends \PHPUnit_Framework_TestCase
{
    protected function getTypeValues()
    {
        return [
            'BigInt' => [new BigNumber(0), new BigNumber('18446744073709551615')],
            'Binary' => 'aG9tZXIgc2ltcHNvbg==',
            'Blob' => 'aG9tZXIgc2ltcHNvbg==',
            'Boolean' => [false, true],
            'Date' => new \DateTime(),
            'DateTime' => new \DateTime(),
            'Decimal' => 3.14,
            'DynamicField' => DynamicField::createIntVal('int_val', 1),
            'Float' => 13213.032468,
            'GeoPoint' => new GeoPoint(0.5, 102.0),
            'IntEnum' => IntEnum::UNKNOWN(),
            'Int' => [0, 4294967295],
            'MediumInt' => [0, 16777215],
            'MediumBlob' => 'aG9tZXIgc2ltcHNvbg==',
            'MediumText' => 'medium text',
            'Message' => NestedMessage::create(),
            'MessageRef' => new MessageRef(NestedMessage::schema()->getCurie(), UuidIdentifier::generate()),
            'Microtime' => Microtime::create(),
            'SignedBigInt' => [new BigNumber('-9223372036854775808'), new BigNumber('9223372036854775807')],
            'SignedMediumInt' => [-8388608, 8388607],
            'SignedSmallInt' => [-32768, 32767],
            'SignedTinyInt' => [-128, 127],
            'SmallInt' => [0, 65535],
            'StringEnum' => StringEnum::UNKNOWN(),
            'String' => 'string',
            'Text' => 'text',
            'TimeUuid' => TimeUuidIdentifier::generate(),
            'Timestamp' => time(),
            'TinyInt' => [0, 255],
            'Uuid' => UuidIdentifier::generate(),
        ];
    }

    protected function getInvalidTypeValues()
    {
        return [
            'BigInt' => [new BigNumber(-1), new BigNumber('18446744073709551616')],
            'Binary' => false,
            'Blob' => false,
            'Boolean' => 'not_a_bool',
            'Date' => 'not_a_date',
            'DateTime' => 'not_a_date',
            'Decimal' => 1,
            'DynamicField' => 'not_a_dynamic_field',
            'Float' => 1,
            'GeoPoint' => 'not_a_geo_point',
            'IntEnum' => Priority::NORMAL(), // not the correct enum
            'Int' => [-1, 4294967296],
            'MediumInt' => [-1, 16777216],
            'MediumBlob' => false,
            'MediumText' => false,
            'Message' => EmailMessage::create(), // not the correct message
            'MessageRef' => 'not_a_message_ref',
            'Microtime' => microtime(),
            'SignedBigInt' => [new BigNumber('-9223372036854775809'), new BigNumber('9223372036854775808')],
            'SignedMediumInt' => [-8388609, 8388608],
            'SignedSmallInt' => [-32769, 32768],
            'SignedTinyInt' => [-129, 128],
            'SmallInt' => [-1, 65536],
            'StringEnum' => Provider::AOL(), // not the correct enum
            'String' => false,
            'Text' => false,
            'TimeUuid' => 'not_a_time_uuid',
            'Timestamp' => 'not_a_timestamp',
            'TinyInt' => [-1, 256],
            'Uuid' => 'not_a_uuid',
        ];
    }

    public function testAddInvalidTypes()
    {
        $message = MapsMessage::create();

        foreach ($this->getInvalidTypeValues() as $k => $v) {
            $thrown = false;
            try {
                if (is_array($v)) {
                    $message->addToMap($k, 'test1', $v[0]);
                    $message->addToMap($k, 'test2', $v[1]);
                } else {
                    $message->addToMap($k, 'test1', $v);
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
        // todo: refactor, this test is weird and confusing
        $shouldWork = MapsMessage::create();
        $shouldFail = clone $shouldWork;

        /*
         * some int types won't fail because they're all ints of course, just different ranges.
         * e.g. an Int is also all other unsigned ints (except BigInt but that's a class so we're fine)
         */
        $allInts = [
            'TinyInt',
            'SmallInt',
            'MediumInt',
            'Int',
            'SignedTinyInt',
            'SignedSmallInt',
            'SignedMediumInt',
            'SignedInt',
            'Timestamp'
        ];
        $allStrings = ['Binary', 'Blob', 'MediumBlob', 'MediumText', 'String', 'Text'];

        foreach ($shouldWork::getAllTypes() as $type => $class) {
            foreach ($this->getTypeValues() as $k => $v) {
                $thrown = false;
                if ($type == $k) {
                    if (is_array($v)) {
                        $shouldWork->addToMap($type, 'test1', $v[0]);
                        $shouldWork->addToMap($type, 'test2', $v[1]);
                    } else {
                        $shouldWork->addToMap($type, 'test1', $v);
                    }
                    continue;
                }

                try {
                    if (is_array($v)) {
                        $shouldFail->addToMap($type, 'test1', $v[0]);
                        $shouldFail->addToMap($type, 'test2', $v[1]);
                    } else {
                        $shouldFail->addToMap($type, 'test1', $v);
                    }

                    switch ($type) {
                        case 'Binary':
                            if (in_array($k, $allStrings)) {
                                continue 2;
                            }
                            break;

                        case 'Blob':
                            if (in_array($k, $allStrings)) {
                                continue 2;
                            }
                            break;

                        case 'Decimal':
                            if (in_array($k, ['Float'])) {
                                continue 2;
                            }
                            break;

                        case 'Date':
                            if (in_array($k, ['DateTime'])) {
                                continue 2;
                            }
                            break;

                        case 'DateTime':
                            if (in_array($k, ['Date'])) {
                                continue 2;
                            }
                            break;

                        case 'Float':
                            if (in_array($k, ['Decimal'])) {
                                continue 2;
                            }
                            break;

                        case 'Identifier':
                            if (in_array($k, ['TimeUuid', 'Uuid'])) {
                                continue 2;
                            }
                            break;

                        case 'MediumBlob':
                            if (in_array($k, $allStrings)) {
                                continue 2;
                            }
                            break;

                        case 'MediumText':
                            if (in_array($k, $allStrings)) {
                                continue 2;
                            }
                            break;

                        case 'String':
                            if (in_array($k, $allStrings)) {
                                continue 2;
                            }
                            break;

                        case 'Text':
                            if (in_array($k, $allStrings)) {
                                continue 2;
                            }
                            break;

                        case 'Timestamp':
                            if (in_array($k, $allInts)) {
                                continue 2;
                            }
                            break;

                        case 'Uuid':
                            if (in_array($k, ['Identifier', 'TimeUuid'])) {
                                continue 2;
                            }
                            break;

                        default:
                    }

                    if (false !== strpos($type, 'Int') && in_array($k, $allInts)) {
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

        //echo json_encode($shouldWork, JSON_PRETTY_PRINT);
    }
}
