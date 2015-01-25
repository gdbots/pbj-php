<?php

namespace Gdbots\Tests\Pbj\Fixtures;

use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\Type as T;
use Gdbots\Tests\Pbj\Fixtures\Enum\Priority;
use Gdbots\Tests\Pbj\Fixtures\Enum\Provider;

class EmailMessage extends AbstractMessage
{
    const FROM_NAME  = 'from_name';
    const FROM_EMAIL = 'from_email';
    const SUBJECT    = 'subject';
    const BODY       = 'body';
    const PRIORITY   = 'priority';
    const SENT       = 'sent';
    const DATE_SENT  = 'date_sent';
    const MICROTIME_SENT = 'microtime_sent';
    const PROVIDER   = 'provider';
    const LABELS     = 'labels';

    /**
     * @return Schema
     */
    protected static function defineSchema()
    {
        return Schema::create(__CLASS__, 'gdbots:tests.pbj:fixtures:email-message:1-0-0', [
            Fb::create(self::FROM_NAME, T\StringType::create())
                ->build(),
            Fb::create(self::FROM_EMAIL, T\StringType::create())
                ->required()
                ->format('email')
                ->build(),
            Fb::create(self::SUBJECT, T\StringType::create())
                ->withDefault(function (EmailMessage $message = null) {
                    // closure func default spice or gtfo and use named automagic defaults?
                    if (!$message) {
                        return null;
                    }
                    return implode(',', $message->getLabels()) . ' test';
                })
                ->build(),
            Fb::create(self::BODY, T\StringType::create())->build(),
            Fb::create(self::PRIORITY, T\IntEnumType::create())
                ->required()
                ->className('Gdbots\Tests\Pbj\Fixtures\Enum\Priority')
                ->withDefault(Priority::NORMAL())
                ->build(),
            Fb::create(self::SENT, T\BooleanType::create())->build(),
            Fb::create(self::DATE_SENT, T\DateTimeType::create())->build(),
            Fb::create(self::MICROTIME_SENT, T\MicrotimeType::create())->build(),
            Fb::create(self::PROVIDER, T\StringEnumType::create())
                ->className('Gdbots\Tests\Pbj\Fixtures\Enum\Provider')
                ->withDefault(Provider::GMAIL())
                ->build(),
            Fb::create(self::LABELS, T\StringType::create())->asASet()->build(),
        ]);
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->get(self::FROM_NAME);
    }

    /**
     * @param string $fromName
     * @return self
     */
    public function setFromName($fromName)
    {
        return $this->setSingleValue(self::FROM_NAME, $fromName);
    }

    /**
     * @return string
     */
    public function getFromEmail()
    {
        return $this->get(self::FROM_EMAIL);
    }

    /**
     * @param string $email
     * @return self
     */
    public function setFromEmail($email)
    {
        return $this->setSingleValue(self::FROM_EMAIL, $email);
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->get(self::SUBJECT);
    }

    /**
     * @param string $subject
     * @return self
     */
    public function setSubject($subject)
    {
        return $this->setSingleValue(self::SUBJECT, $subject);
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->get(self::BODY);
    }

    /**
     * @param string $body
     * @return self
     */
    public function setBody($body)
    {
        return $this->setSingleValue(self::BODY, $body);
    }

    /**
     * @return Priority
     */
    public function getPriority()
    {
        return $this->get(self::PRIORITY);
    }

    /**
     * @param Priority $priority
     * @return self
     */
    public function setPriority(Priority $priority)
    {
        return $this->setSingleValue(self::PRIORITY, $priority);
    }

    /**
     * @return bool
     */
    public function wasSent()
    {
        return $this->get(self::SENT);
    }

    /**
     * @return self
     */
    public function markAsSent()
    {
        return $this->setSingleValue(self::SENT, true);
    }

    /**
     * @return \DateTime
     */
    public function getDateSent()
    {
        return $this->get(self::DATE_SENT);
    }

    /**
     * @return Provider
     */
    public function getProvider()
    {
        return $this->get(self::PROVIDER);
    }

    /**
     * @param Provider $provider
     * @return self
     */
    public function setProvider(Provider $provider)
    {
        return $this->setSingleValue(self::PROVIDER, $provider);
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return $this->get(self::LABELS) ?: [];
    }

    /**
     * @param string $label
     * @return self
     */
    public function addLabel($label)
    {
        return $this->addToSet(self::LABELS, [$label]);
    }

    /**
     * @param string $label
     * @return self
     */
    public function removeLabel($label)
    {
        return $this->removeFromSet(self::LABELS, [$label]);
    }
}