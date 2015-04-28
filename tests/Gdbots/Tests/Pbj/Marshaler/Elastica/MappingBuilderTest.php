<?php

namespace Gdbots\Tests\Pbj\Marshaler\Elastica;

use Elastica\Client;
use Elastica\Index;
use Elastica\Type;
use Gdbots\Pbj\Marshaler\Elastica\MappingBuilder;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;

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
        echo json_encode($mapping->toArray(), JSON_PRETTY_PRINT);
    }
}
