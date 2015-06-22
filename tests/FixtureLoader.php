<?php

namespace Gdbots\Tests\Pbj;

use Gdbots\Pbj\Serializer\JsonSerializer;
use Gdbots\Pbj\Serializer\Serializer;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;

trait FixtureLoader
{
    /** @var Serializer */
    protected static $serializer;

    /** @var EmailMessage */
    protected static $emailMessageFixture;

    /**
     * @return Serializer
     */
    protected function getSerializer()
    {
        if (null === self::$serializer) {
            self::$serializer = new JsonSerializer();
        }
        return self::$serializer;
    }

    /**
     * @return EmailMessage
     */
    protected function createEmailMessage()
    {
        if (null === self::$emailMessageFixture) {
            $json = file_get_contents(__DIR__ . '/Fixtures/email-message.json');
            self::$emailMessageFixture = $this->getSerializer()->deserialize($json);
        }
        return clone self::$emailMessageFixture;
    }
}
