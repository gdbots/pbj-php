<?php

namespace Gdbots\Tests\Pbj\Marshaler\Elastica;

use Elastica\Client;
use Elastica\Index;
use Elastica\Type;
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
        $schema = EmailMessage::schema();
        $mapping = $this->factory->create($schema);
        $mapping->setType(
            new Type(
                new Index(new Client(), $this->indexName),
                'pbj_test_type'
            )
        );

        $expected = '{"pbj_test_type":{"properties":{"_schema":{"type":"string","index":"not_analyzed"},"id":{"type":"string","index":"not_analyzed"},"from_name":{"type":"string"},"from_email":{"type":"string","index":"not_analyzed"},"subject":{"type":"string"},"body":{"type":"string"},"priority":{"type":"integer"},"sent":{"type":"boolean"},"date_sent":{"type":"date"},"microtime_sent":{"type":"long"},"provider":{"type":"string","index":"not_analyzed"},"labels":{"type":"string","analyzer":"pbj_keyword_analyzer"},"nested":{"type":"nested","properties":{"_schema":{"type":"string","index":"not_analyzed"},"test1":{"type":"string"},"test2":{"type":"long"},"location":{"type":"geo_point"},"refs":{"type":"object","properties":{"curie":{"type":"string","index":"not_analyzed"},"id":{"type":"string","index":"not_analyzed"},"tag":{"type":"string","index":"not_analyzed"}}}}},"enum_in_set":{"type":"string","index":"not_analyzed"},"enum_in_list":{"type":"string","index":"not_analyzed"},"any_of_message":{"type":"nested","properties":{"_schema":{"type":"string","index":"not_analyzed"}}}}}}';
        $this->assertSame(json_encode($mapping->toArray()), $expected);
        //echo json_encode($mapping->toArray(), JSON_PRETTY_PRINT);

        $schema = MapsMessage::schema();
        $mapping = $this->factory->create($schema);
        $mapping->setType(
            new Type(
                new Index(new Client(), $this->indexName),
                'pbj_test_type'
            )
        );

        $expected = '{"pbj_test_type":{"properties":{"_schema":{"type":"string","index":"not_analyzed"},"BigInt":{"type":"long"},"Binary":{"type":"binary"},"Blob":{"type":"binary"},"Boolean":{"type":"boolean"},"DateTime":{"type":"date"},"Date":{"type":"date"},"Decimal":{"type":"double"},"Float":{"type":"float"},"GeoPoint":{"type":"geo_point"},"Identifier":{"type":"string","index":"not_analyzed"},"IntEnum":{"type":"integer"},"Int":{"type":"long"},"MediumBlob":{"type":"binary"},"MediumInt":{"type":"integer"},"MediumText":{"type":"string"},"MessageRef":{"type":"object","properties":{"curie":{"type":"string","index":"not_analyzed"},"id":{"type":"string","index":"not_analyzed"},"tag":{"type":"string","index":"not_analyzed"}}},"Message":{"type":"nested","properties":{"_schema":{"type":"string","index":"not_analyzed"},"test1":{"type":"string"},"test2":{"type":"long"},"location":{"type":"geo_point"},"refs":{"type":"object","properties":{"curie":{"type":"string","index":"not_analyzed"},"id":{"type":"string","index":"not_analyzed"},"tag":{"type":"string","index":"not_analyzed"}}}}},"Microtime":{"type":"long"},"SignedBigInt":{"type":"long"},"SignedInt":{"type":"integer"},"SignedMediumInt":{"type":"long"},"SignedSmallInt":{"type":"short"},"SignedTinyInt":{"type":"byte"},"SmallInt":{"type":"integer"},"StringEnum":{"type":"string","index":"not_analyzed"},"String":{"type":"string"},"Text":{"type":"string"},"Timestamp":{"type":"date"},"TimeUuid":{"type":"string","index":"not_analyzed"},"TinyInt":{"type":"short"},"Uuid":{"type":"string","index":"not_analyzed"}}}}';
        $this->assertSame(json_encode($mapping->toArray()), $expected);
        //echo json_encode($mapping->toArray(), JSON_PRETTY_PRINT);
    }
}
