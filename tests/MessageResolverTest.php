<?php

namespace Gdbots\Tests\Pbj;

use Gdbots\Pbj\MessageResolver;
use Gdbots\Pbj\SchemaId;
use Gdbots\Pbj\SchemaQName;

class MessageResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testResolveQName()
    {
        $schemaId = SchemaId::fromString('pbj:acme:blog:node:article:1-0-0');
        MessageResolver::register($schemaId, 'Fake');

        $curie = MessageResolver::resolveQName(SchemaQName::fromString('acme:article'));
        $this->assertSame($schemaId->getCurie(), $curie);
    }

    /**
     * @expectedException \Gdbots\Pbj\Exception\NoMessageForQName
     */
    public function testResolveInvalidQName()
    {
        $curie = MessageResolver::resolveQName(SchemaQName::fromString('acme:video'));
    }
}
