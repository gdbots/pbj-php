<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj\Marshaler\Elastica;

use Gdbots\Pbj\Marshaler\Elastica\MappingBuilder;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;
use Gdbots\Tests\Pbj\Fixtures\MapsMessage;
use PHPUnit\Framework\TestCase;

class MappingBuilderTest extends TestCase
{
    public function testCreate()
    {
        $this->markTestIncomplete();
        $builder = new MappingBuilder();
        $builder->addSchema(EmailMessage::schema());
        $builder->addSchema(MapsMessage::schema());
        $mapping = $builder->build();
        // echo json_encode($mapping->toArray(), JSON_PRETTY_PRINT);
    }
}
