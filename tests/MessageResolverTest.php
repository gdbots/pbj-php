<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj;

use Gdbots\Pbj\Exception\MoreThanOneMessageForMixin;
use Gdbots\Pbj\Exception\NoMessageForMixin;
use Gdbots\Pbj\Exception\NoMessageForQName;
use Gdbots\Pbj\MessageResolver;
use Gdbots\Pbj\SchemaQName;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;
use Gdbots\Tests\Pbj\Fixtures\NestedMessage;
use PHPUnit\Framework\TestCase;

class MessageResolverTest extends TestCase
{
    public function testResolveQName()
    {
        $class = MessageResolver::resolveQName('*:email-message');
        $this->assertSame(EmailMessage::class, $class);
    }

    public function testResolveInvalidQName()
    {
        $this->expectException(NoMessageForQName::class);
        MessageResolver::resolveQName(SchemaQName::fromString('acme:video'));
    }

    public function testHasCurie()
    {
        $this->assertFalse(MessageResolver::hasCurie('vendor:package:category:message'));
        $this->assertTrue(MessageResolver::hasCurie(EmailMessage::schema()->getCurie()));
    }

    public function testHasQName()
    {
        $this->assertFalse(MessageResolver::hasQName('acme:nope'));
        $this->assertTrue(MessageResolver::hasQName('*:email-message'));
    }

    public function testFindAllUsingMixin()
    {
        $curies = MessageResolver::findAllUsingMixin('gdbots:tests.pbj:mixin:many:v1');
        $this->assertSame([
            EmailMessage::schema()->getCurieMajor(),
            NestedMessage::schema()->getCurieMajor(),
        ],
            $curies
        );
    }

    public function testFindAllUsingMixinWhenNone()
    {
        $this->expectException(NoMessageForMixin::class);
        MessageResolver::findAllUsingMixin('gdbots:tests.pbj:mixin:fake:v1');
    }

    public function testFindOneUsingMixinWhenOne()
    {
        $curie = MessageResolver::findOneUsingMixin('gdbots:tests.pbj:mixin:one:v1');
        $this->assertSame(EmailMessage::schema()->getCurieMajor(), $curie);
    }

    public function testFindOneUsingMixinWhenMany()
    {
        $this->expectException(MoreThanOneMessageForMixin::class);
        MessageResolver::findOneUsingMixin('gdbots:tests.pbj:mixin:many:v1');
    }
}
