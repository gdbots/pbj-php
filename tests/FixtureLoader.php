<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj;

use Gdbots\Pbj\Serializer\JsonSerializer;
use Gdbots\Pbj\Serializer\Serializer;
use Gdbots\Pbj\Util\DateUtil;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;

trait FixtureLoader
{
    protected static ?Serializer $serializer = null;
    protected static ?EmailMessage $emailMessageFixture = null;

    protected function getSerializer(): Serializer
    {
        if (null === self::$serializer) {
            self::$serializer = new JsonSerializer();
        }

        return self::$serializer;
    }

    protected function createEmailMessage(): EmailMessage
    {
        if (null === self::$emailMessageFixture) {
            $json = file_get_contents(__DIR__ . '/Fixtures/email-message.json');
            self::$emailMessageFixture = $this->getSerializer()->deserialize($json);
        }

        $message = clone self::$emailMessageFixture;

        // fixme: handle clipping microseconds due to clone issue.  see issue #15
        $date = \DateTime::createFromFormat(DateUtil::ISO8601_ZULU, '2014-12-25T12:12:00.123456Z');
        $message->set('date_sent', $date);

        return $message;
    }
}
