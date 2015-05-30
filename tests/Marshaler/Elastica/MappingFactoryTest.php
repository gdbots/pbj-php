<?php

namespace Gdbots\Tests\Pbj\Marshaler\Elastica;

use Elastica\Client;
use Elastica\Index;
use Elastica\Type;
use Elastica\Type\Mapping;
use Gdbots\Pbj\Marshaler\Elastica\MappingFactory;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;
use Gdbots\Tests\Pbj\Fixtures\MapsMessage;

class MappingFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var MappingFactory */
    protected $factory;

    /** @var string */
    protected $indexName;

    public function setup()
    {
        $this->factory = new MappingFactory();
        $this->indexName = getenv('ELASTICA_INDEX') ?: 'pbj_tests';
    }

    public function testCreate()
    {
        $type = new Type(new Index(new Client(), $this->indexName), 'pbj_test_type');
        $schema = EmailMessage::schema();
        $properties = $this->factory->create($schema);
        $mapping = new Mapping($type, $properties);

        $expected = '{"pbj_test_type":{"properties":{"_schema":{"type":"string","index":"not_analyzed","include_in_all":false},"id":{"type":"string","index":"not_analyzed","include_in_all":false},"from_name":{"type":"string"},"from_email":{"type":"string","index":"not_analyzed","include_in_all":false},"subject":{"type":"string"},"body":{"type":"string"},"priority":{"type":"integer","include_in_all":false},"sent":{"type":"boolean","include_in_all":false},"date_sent":{"type":"date","include_in_all":false},"microtime_sent":{"type":"long","include_in_all":false},"provider":{"type":"string","index":"not_analyzed","include_in_all":false},"labels":{"type":"string","analyzer":"pbj_keyword_analyzer","include_in_all":false},"nested":{"type":"nested","properties":{"_schema":{"type":"string","index":"not_analyzed","include_in_all":false},"test1":{"type":"string"},"test2":{"type":"long","include_in_all":false},"location":{"type":"geo_point","include_in_all":false},"refs":{"type":"object","properties":{"curie":{"type":"string","index":"not_analyzed","include_in_all":false},"id":{"type":"string","index":"not_analyzed","include_in_all":false},"tag":{"type":"string","index":"not_analyzed","include_in_all":false}}}}},"enum_in_set":{"type":"string","index":"not_analyzed","include_in_all":false},"enum_in_list":{"type":"string","index":"not_analyzed","include_in_all":false},"any_of_message":{"type":"nested","properties":{"_schema":{"type":"string","index":"not_analyzed","include_in_all":false}}}}}}';
        $this->assertSame(json_encode($mapping->toArray()), $expected);
        //echo json_encode($mapping->toArray(), JSON_PRETTY_PRINT);

        $schema = MapsMessage::schema();
        $properties = $this->factory->create($schema);
        $mapping = new Mapping($type, $properties);

        $expected = '{"pbj_test_type":{"properties":{"_schema":{"type":"string","index":"not_analyzed","include_in_all":false},"BigInt":{"type":"long","include_in_all":false},"Binary":{"type":"binary"},"Blob":{"type":"binary"},"Boolean":{"type":"boolean","include_in_all":false},"DateTime":{"type":"date","include_in_all":false},"Date":{"type":"date","include_in_all":false},"Decimal":{"type":"double","include_in_all":false},"Float":{"type":"float","include_in_all":false},"GeoPoint":{"type":"geo_point","include_in_all":false},"Identifier":{"type":"string","index":"not_analyzed","include_in_all":false},"IntEnum":{"type":"integer","include_in_all":false},"Int":{"type":"long","include_in_all":false},"MediumBlob":{"type":"binary"},"MediumInt":{"type":"integer","include_in_all":false},"MediumText":{"type":"string"},"MessageRef":{"type":"object","properties":{"curie":{"type":"string","index":"not_analyzed","include_in_all":false},"id":{"type":"string","index":"not_analyzed","include_in_all":false},"tag":{"type":"string","index":"not_analyzed","include_in_all":false}}},"Message":{"type":"nested","properties":{"_schema":{"type":"string","index":"not_analyzed","include_in_all":false},"test1":{"type":"string"},"test2":{"type":"long","include_in_all":false},"location":{"type":"geo_point","include_in_all":false},"refs":{"type":"object","properties":{"curie":{"type":"string","index":"not_analyzed","include_in_all":false},"id":{"type":"string","index":"not_analyzed","include_in_all":false},"tag":{"type":"string","index":"not_analyzed","include_in_all":false}}}}},"Microtime":{"type":"long","include_in_all":false},"SignedBigInt":{"type":"long","include_in_all":false},"SignedInt":{"type":"integer","include_in_all":false},"SignedMediumInt":{"type":"long","include_in_all":false},"SignedSmallInt":{"type":"short","include_in_all":false},"SignedTinyInt":{"type":"byte","include_in_all":false},"SmallInt":{"type":"integer","include_in_all":false},"StringEnum":{"type":"string","index":"not_analyzed","include_in_all":false},"String":{"type":"string"},"Text":{"type":"string"},"Timestamp":{"type":"date","include_in_all":false},"TimeUuid":{"type":"string","index":"not_analyzed","include_in_all":false},"TinyInt":{"type":"short","include_in_all":false},"Uuid":{"type":"string","index":"not_analyzed","include_in_all":false}}}}';
        $this->assertSame(json_encode($mapping->toArray()), $expected);
        //echo json_encode($mapping->toArray(), JSON_PRETTY_PRINT);
    }
}
