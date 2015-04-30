<?php

namespace Gdbots\Tests\Pbj\Marshaler\Elastica;

use Elastica\Client;
use Elastica\Index;
use Elastica\Type;
use Gdbots\Pbj\Marshaler\Elastica\MappingBuilder;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;
use Gdbots\Tests\Pbj\Fixtures\MapsMessage;

class MappingBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var MappingBuilder */
    protected $builder;

    public function setup()
    {
        $this->builder = new MappingBuilder();
    }

    public function testBuild()
    {
        $schema = EmailMessage::schema();
        $mapping = $this->builder->build($schema);
        $mapping->setType(
            new Type(
                new Index(new Client(), 'pbj_test'),
                'pbj_test_type'
            )
        );

        $expected = '{"pbj_test_type":{"properties":{"_schema":{"type":"string","index":"not_analyzed"},"id":{"type":"string","index":"not_analyzed"},"from_name":{"type":"string"},"from_email":{"type":"string","index":"not_analyzed"},"subject":{"type":"string"},"body":{"type":"string"},"priority":{"type":"integer"},"sent":{"type":"boolean"},"date_sent":{"type":"dateOptionalTime"},"microtime_sent":{"type":"long"},"provider":{"type":"string","index":"not_analyzed"},"labels":{"type":"string","index":"not_analyzed","fields":{"keyword":{"type":"string","index":"analyzer_keyword"}}},"nested":{"type":"nested","properties":{"_schema":{"type":"string","index":"not_analyzed"},"test1":{"type":"string"},"test2":{"type":"long"},"location":{"type":"geo_point"}}},"enum_in_set":{"type":"string","index":"not_analyzed"},"enum_in_list":{"type":"string","index":"not_analyzed"},"any_of_message":{"type":"nested"}}}}';
        $this->assertSame(json_encode($mapping->toArray()), $expected);
        //echo json_encode($mapping->toArray(), JSON_PRETTY_PRINT);


        $schema = MapsMessage::schema();
        $mapping = $this->builder->build($schema);
        $mapping->setType(
            new Type(
                new Index(new Client(), 'pbj_test'),
                'pbj_test_type'
            )
        );

        $expected = '{"pbj_test_type":{"properties":{"_schema":{"type":"string","index":"not_analyzed"},"BigInt":{"type":"long"},"Binary":{"type":"binary"},"Blob":{"type":"binary"},"Boolean":{"type":"boolean"},"DateTime":{"type":"dateOptionalTime"},"Date":{"type":"dateOptionalTime"},"Decimal":{"type":"double"},"Float":{"type":"float"},"GeoPoint":{"type":"geo_point"},"Identifier":{"type":"string","index":"not_analyzed"},"IntEnum":{"type":"integer"},"Int":{"type":"long"},"MediumBlob":{"type":"binary"},"MediumInt":{"type":"integer"},"MediumText":{"type":"string"},"MessageRef":{"type":"object","properties":{"curie":{"type":"string","index":"not_analyzed"},"id":{"type":"string","index":"not_analyzed"},"tag":{"type":"string","index":"not_analyzed"}}},"Message":{"type":"nested","properties":{"_schema":{"type":"string","index":"not_analyzed"},"test1":{"type":"string"},"test2":{"type":"long"},"location":{"type":"geo_point"}}},"Microtime":{"type":"long"},"SignedBigInt":{"type":"long"},"SignedInt":{"type":"integer"},"SignedMediumInt":{"type":"long"},"SignedSmallInt":{"type":"short"},"SignedTinyInt":{"type":"byte"},"SmallInt":{"type":"integer"},"StringEnum":{"type":"string","index":"not_analyzed"},"String":{"type":"string"},"Text":{"type":"string"},"Timestamp":{"type":"dateOptionalTime"},"TimeUuid":{"type":"string","index":"not_analyzed"},"TinyInt":{"type":"short"},"Uuid":{"type":"string","index":"not_analyzed"}}}}';
        $this->assertSame(json_encode($mapping->toArray()), $expected);
        //echo json_encode($mapping->toArray(), JSON_PRETTY_PRINT);
    }
}
