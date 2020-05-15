<?php
declare(strict_types=1);

namespace Gdbots\Tests\Pbj;

use Gdbots\Common\Util\DateUtils;
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

        $message = clone self::$emailMessageFixture;

        // fixme: handle clipping microseconds due to clone issue.  see issue #15
        $date = \DateTime::createFromFormat(DateUtils::ISO8601_ZULU, '2014-12-25T12:12:00.123456Z');
        $message->set('date_sent', $date);

        return $message;
    }
}
