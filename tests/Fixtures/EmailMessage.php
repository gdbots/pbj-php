<?php

namespace Gdbots\Tests\Pbj\Fixtures;

use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\AbstractMessage;
use Gdbots\Pbj\Enum\Format;
use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\MessageRef;
use Gdbots\Pbj\MessageResolver;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\Type as T;
use Gdbots\Tests\Pbj\Fixtures\Enum\Priority;
use Gdbots\Tests\Pbj\Fixtures\Enum\Provider;

final class EmailMessage extends AbstractMessage
{
    /**
     * @return Schema
     */
    protected static function defineSchema()
    {
        $schema = new Schema('pbj:gdbots:tests.pbj:fixtures:email-message:1-0-0', __CLASS__,
            [
                Fb::create('id', T\TimeUuidType::create())
                    //->useTypeDefault(false)
                    ->required()
                    ->build(),
                Fb::create('from_name', T\StringType::create())
                    ->build(),
                Fb::create('from_email', T\StringType::create())
                    ->required()
                    ->format('email')
                    ->build(),
                Fb::create('subject', T\StringType::create())
                    ->withDefault(function (EmailMessage $message = null) {
                        // closure func default spice or gtfo and use named automagic defaults?
                        if (!$message) {
                            return null;
                        }
                        return implode(',', $message->getLabels()) . ' test';
                    })
                    ->build(),
                Fb::create('body', T\StringType::create())->build(),
                Fb::create('priority', T\IntEnumType::create())
                    ->required()
                    ->className('Gdbots\Tests\Pbj\Fixtures\Enum\Priority')
                    ->withDefault(Priority::NORMAL)
                    ->build(),
                Fb::create('sent', T\BooleanType::create())->build(),
                Fb::create('date_sent', T\DateTimeType::create())->build(),
                Fb::create('microtime_sent', T\MicrotimeType::create())->build(),
                Fb::create('provider', T\StringEnumType::create())
                    ->className('Gdbots\Tests\Pbj\Fixtures\Enum\Provider')
                    ->withDefault(Provider::GMAIL())
                    ->build(),
                Fb::create('labels', T\StringType::create())
                    ->format(Format::HASHTAG())
                    ->asASet()
                    ->build(),
                Fb::create('nested', T\MessageType::create())
                    ->className('Gdbots\Tests\Pbj\Fixtures\NestedMessage')
                    ->build(),
                Fb::create('enum_in_set', T\StringEnumType::create())
                    ->className('Gdbots\Tests\Pbj\Fixtures\Enum\Provider')
                    ->asASet()
                    ->build(),
                Fb::create('enum_in_list', T\StringEnumType::create())
                    ->className('Gdbots\Tests\Pbj\Fixtures\Enum\Provider')
                    ->asAList()
                    ->build(),
                Fb::create('any_of_message', T\MessageType::create())
                    ->className('Gdbots\Pbj\Message')
                    ->asAList()
                    ->build(),
            ]
        );

        MessageResolver::registerSchema($schema);
        return $schema;
    }

    /**
     * {@inheritdoc}
     */
    public function generateMessageRef($tag = null)
    {
        return new MessageRef(static::schema()->getCurie(), $this->getMessageId(), $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function getUriTemplateVars()
    {
        return ['id' => $this->getMessageId()->toString()];
    }

    /**
     * @return UuidIdentifier
     */
    public function getMessageId()
    {
        return $this->get('id');
    }

    /**
     * @param UuidIdentifier $id
     * @return self
     */
    public function setMessageId(UuidIdentifier $id)
    {
        return $this->setSingleValue('id', $id);
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->get('from_name');
    }

    /**
     * @param string $fromName
     * @return self
     */
    public function setFromName($fromName)
    {
        return $this->setSingleValue('from_name', $fromName);
    }

    /**
     * @return string
     */
    public function getFromEmail()
    {
        return $this->get('from_email');
    }

    /**
     * @param string $email
     * @return self
     */
    public function setFromEmail($email)
    {
        return $this->setSingleValue('from_email', $email);
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->get('subject');
    }

    /**
     * @param string $subject
     * @return self
     */
    public function setSubject($subject)
    {
        return $this->setSingleValue('subject', $subject);
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->get('body');
    }

    /**
     * @param string $body
     * @return self
     */
    public function setBody($body)
    {
        return $this->setSingleValue('body', $body);
    }

    /**
     * @return Priority
     */
    public function getPriority()
    {
        return $this->get('priority');
    }

    /**
     * @param Priority $priority
     * @return self
     */
    public function setPriority(Priority $priority)
    {
        return $this->setSingleValue('priority', $priority);
    }

    /**
     * @return bool
     */
    public function wasSent()
    {
        return $this->get('sent');
    }

    /**
     * @return self
     */
    public function markAsSent()
    {
        return $this->setSingleValue('sent', true);
    }

    /**
     * @return \DateTime
     */
    public function getDateSent()
    {
        return $this->get('date_sent');
    }

    /**
     * @return \Gdbots\Common\Microtime
     */
    public function getMicrotimeSent()
    {
        return $this->get('microtime_sent');
    }

    /**
     * @return Provider
     */
    public function getProvider()
    {
        return $this->get('provider');
    }

    /**
     * @param Provider $provider
     * @return self
     */
    public function setProvider(Provider $provider)
    {
        return $this->setSingleValue('provider', $provider);
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return $this->get('labels') ? : [];
    }

    /**
     * @param string $label
     * @return self
     */
    public function addLabel($label)
    {
        return $this->addToSet('labels', [$label]);
    }

    /**
     * @param string $label
     * @return self
     */
    public function removeLabel($label)
    {
        return $this->removeFromSet('labels', [$label]);
    }

    /**
     * @return NestedMessage
     */
    public function getNested()
    {
        return $this->get('nested');
    }

    /**
     * @param NestedMessage $nested
     * @return self
     */
    public function setNested(NestedMessage $nested)
    {
        return $this->setSingleValue('nested', $nested);
    }
}
