<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj;

use Gdbots\Pbj\Exception\NoMessageForQName;
use Gdbots\Pbj\MessageResolver;
use Gdbots\Pbj\SchemaQName;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;
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
}
