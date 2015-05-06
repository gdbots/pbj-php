<?php

namespace Gdbots\Tests\Pbj\Fixtures;

use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\Enum\Format;
use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\MessageResolver;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\Type as T;
use Gdbots\Tests\Pbj\Fixtures\Enum\Priority;
use Gdbots\Tests\Pbj\Fixtures\Enum\Provider;

final class EmailMessage extends AbstractMessage
{
    const MESSAGE_ID_FIELD_NAME = 'id';
    const FROM_NAME_FIELD_NAME = 'from_name';
    const FROM_EMAIL_FIELD_NAME = 'from_email';
    const SUBJECT_FIELD_NAME = 'subject';
    const BODY_FIELD_NAME = 'body';
    const PRIORITY_FIELD_NAME = 'priority';
    const SENT_FIELD_NAME = 'sent';
    const DATE_SENT_FIELD_NAME = 'date_sent';
    const MICROTIME_SENT_FIELD_NAME = 'microtime_sent';
    const PROVIDER_FIELD_NAME = 'provider';
    const LABELS_FIELD_NAME = 'labels';
    const NESTED_FIELD_NAME = 'nested';
    const ENUM_IN_SET_FIELD_NAME = 'enum_in_set';
    const ENUM_IN_LIST_FIELD_NAME = 'enum_in_list';
    const ANY_OF_MESSAGE_FIELD_NAME = 'any_of_message';

    /**
     * @return Schema
     */
    protected static function defineSchema()
    {
        $schema = new Schema('pbj:gdbots:tests.pbj:fixtures:email-message:1-0-0', __CLASS__,
                [
                        Fb::create(self::MESSAGE_ID_FIELD_NAME, T\TimeUuidType::create())
                                //->useTypeDefault(false)
                                ->required()
                                ->build(),
                        Fb::create(self::FROM_NAME_FIELD_NAME, T\StringType::create())
                                ->build(),
                        Fb::create(self::FROM_EMAIL_FIELD_NAME, T\StringType::create())
                                ->required()
                                ->format('email')
                                ->build(),
                        Fb::create(self::SUBJECT_FIELD_NAME, T\StringType::create())
                                ->withDefault(function (EmailMessage $message = null) {
                                    // closure func default spice or gtfo and use named automagic defaults?
                                    if (!$message) {
                                        return null;
                                    }
                                    return implode(',', $message->getLabels()) . ' test';
                                })
                                ->build(),
                        Fb::create(self::BODY_FIELD_NAME, T\StringType::create())->build(),
                        Fb::create(self::PRIORITY_FIELD_NAME, T\IntEnumType::create())
                                ->required()
                                ->className('Gdbots\Tests\Pbj\Fixtures\Enum\Priority')
                                ->withDefault(Priority::NORMAL)
                                ->build(),
                        Fb::create(self::SENT_FIELD_NAME, T\BooleanType::create())->build(),
                        Fb::create(self::DATE_SENT_FIELD_NAME, T\DateTimeType::create())->build(),
                        Fb::create(self::MICROTIME_SENT_FIELD_NAME, T\MicrotimeType::create())->build(),
                        Fb::create(self::PROVIDER_FIELD_NAME, T\StringEnumType::create())
                                ->className('Gdbots\Tests\Pbj\Fixtures\Enum\Provider')
                                ->withDefault(Provider::GMAIL())
                                ->build(),
                        Fb::create(self::LABELS_FIELD_NAME, T\StringType::create())
                                ->format(Format::HASHTAG())
                                ->asASet()
                                ->build(),
                        Fb::create(self::NESTED_FIELD_NAME, T\MessageType::create())
                                ->className('Gdbots\Tests\Pbj\Fixtures\NestedMessage')
                                ->build(),
                        Fb::create(self::ENUM_IN_SET_FIELD_NAME, T\StringEnumType::create())
                                ->className('Gdbots\Tests\Pbj\Fixtures\Enum\Provider')
                                ->asASet()
                                ->build(),
                        Fb::create(self::ENUM_IN_LIST_FIELD_NAME, T\StringEnumType::create())
                                ->className('Gdbots\Tests\Pbj\Fixtures\Enum\Provider')
                                ->asAList()
                                ->build(),
                        Fb::create(self::ANY_OF_MESSAGE_FIELD_NAME, T\MessageType::create())
                                ->className('Gdbots\Pbj\Message')
                                ->asAList()
                                ->build(),
                ]
        );

        MessageResolver::registerSchema($schema);
        return $schema;
    }

    /**
     * @return UuidIdentifier
     */
    public function getMessageId()
    {
        return $this->get(self::MESSAGE_ID_FIELD_NAME);
    }

    /**
     * @param UuidIdentifier $id
     * @return self
     */
    public function setMessageId(UuidIdentifier $id)
    {
        return $this->setSingleValue(self::MESSAGE_ID_FIELD_NAME, $id);
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->get(self::FROM_NAME_FIELD_NAME);
    }

    /**
     * @param string $fromName
     * @return self
     */
    public function setFromName($fromName)
    {
        return $this->setSingleValue(self::FROM_NAME_FIELD_NAME, $fromName);
    }

    /**
     * @return string
     */
    public function getFromEmail()
    {
        return $this->get(self::FROM_EMAIL_FIELD_NAME);
    }

    /**
     * @param string $email
     * @return self
     */
    public function setFromEmail($email)
    {
        return $this->setSingleValue(self::FROM_EMAIL_FIELD_NAME, $email);
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->get(self::SUBJECT_FIELD_NAME);
    }

    /**
     * @param string $subject
     * @return self
     */
    public function setSubject($subject)
    {
        return $this->setSingleValue(self::SUBJECT_FIELD_NAME, $subject);
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->get(self::BODY_FIELD_NAME);
    }

    /**
     * @param string $body
     * @return self
     */
    public function setBody($body)
    {
        return $this->setSingleValue(self::BODY_FIELD_NAME, $body);
    }

    /**
     * @return Priority
     */
    public function getPriority()
    {
        return $this->get(self::PRIORITY_FIELD_NAME);
    }

    /**
     * @param Priority $priority
     * @return self
     */
    public function setPriority(Priority $priority)
    {
        return $this->setSingleValue(self::PRIORITY_FIELD_NAME, $priority);
    }

    /**
     * @return bool
     */
    public function wasSent()
    {
        return $this->get(self::SENT_FIELD_NAME);
    }

    /**
     * @return self
     */
    public function markAsSent()
    {
        return $this->setSingleValue(self::SENT_FIELD_NAME, true);
    }

    /**
     * @return \DateTime
     */
    public function getDateSent()
    {
        return $this->get(self::DATE_SENT_FIELD_NAME);
    }

    /**
     * @return \Gdbots\Common\Microtime
     */
    public function getMicrotimeSent()
    {
        return $this->get(self::MICROTIME_SENT_FIELD_NAME);
    }

    /**
     * @return Provider
     */
    public function getProvider()
    {
        return $this->get(self::PROVIDER_FIELD_NAME);
    }

    /**
     * @param Provider $provider
     * @return self
     */
    public function setProvider(Provider $provider)
    {
        return $this->setSingleValue(self::PROVIDER_FIELD_NAME, $provider);
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return $this->get(self::LABELS_FIELD_NAME) ? : [];
    }

    /**
     * @param string $label
     * @return self
     */
    public function addLabel($label)
    {
        return $this->addToSet(self::LABELS_FIELD_NAME, [$label]);
    }

    /**
     * @param string $label
     * @return self
     */
    public function removeLabel($label)
    {
        return $this->removeFromSet(self::LABELS_FIELD_NAME, [$label]);
    }

    /**
     * @return NestedMessage
     */
    public function getNested()
    {
        return $this->get(self::NESTED_FIELD_NAME);
    }

    /**
     * @param NestedMessage $nested
     * @return self
     */
    public function setNested(NestedMessage $nested)
    {
        return $this->setSingleValue(self::NESTED_FIELD_NAME, $nested);
    }
}