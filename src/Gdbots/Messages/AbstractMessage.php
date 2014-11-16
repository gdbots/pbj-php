<?php

namespace Gdbots\Messages;

use Gdbots\Common\FromArrayInterface;
use Gdbots\Common\ToArrayInterface;
use Gdbots\Common\Util\StringUtils;

abstract class AbstractMessage implements MessageInterface, FromArrayInterface, ToArrayInterface
{
    const TYPE_DOUBLE = 1;
    const TYPE_FLOAT = 2;
    const TYPE_INT64 = 3;
    const TYPE_UINT64 = 4;
    const TYPE_INT32 = 5;
    const TYPE_FIXED64 = 6;
    const TYPE_FIXED32 = 7;
    const TYPE_BOOL = 8;
    const TYPE_STRING = 9;
    const TYPE_GROUP = 10;
    const TYPE_MESSAGE = 11;
    const TYPE_BYTES = 12;
    const TYPE_UINT32 = 13;
    const TYPE_ENUM = 14;
    const TYPE_SFIXED32 = 15;
    const TYPE_SFIXED64 = 16;
    const TYPE_SINT32 = 17;
    const TYPE_SINT64 = 18;
    const MAX_FIELD_TYPE = 18;
    const PHP_MESSAGE_OPTION = 0;

    /* @var MessageActor */
    private $actor;

    /* @var MessageMeta */
    private $meta;

    /**
     * @param array $data
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data = array())
    {
        foreach ($data as $key => $value) {
            if (MessageMeta::META === $key) {
                $this->meta = new MessageMeta($this, $value);
                continue;
            }

            if (MessageActor::ACTOR === $key) {
                $this->actor = new MessageActor($value);
                continue;
            }

            $method = 'set' . StringUtils::toCamelCaseFromSnakeCase($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
                continue;
            }

            if ('actor' === $key || 'meta' === $key) {
                throw new \InvalidArgumentException(sprintf('The property [%s] is reserved.', $key));
            }

            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        if (!$this->meta instanceof MessageMeta) {
            $this->meta = new MessageMeta($this, array(
                    MessageMeta::CURIE => MessageCurie::fromMessage($this),
                    MessageMeta::MESSAGE_ID => $this->generateMessageId(),
                ));
        }

        if (!$this->actor instanceof MessageActor) {
            $this->actor = new MessageActor();
        }
    }

    /**
     * Default message version is 1.  Version is only stored
     * or used if it's greater than 1.
     *
     * @return int
     */
    public function getMessageVersion()
    {
        return 1;
    }

    /**
     * @see MessageInterface::meta
     */
    public final function meta()
    {
        return $this->meta;
    }

    /**
     * @see MessageInterface::actor
     */
    public final function actor()
    {
        return $this->actor;
    }

    /**
     * @param array $data
     * @return MessageInterface|CommandBus\CommandInterface|EventBus\DomainEventInterface
     */
    public static function fromArray(array $data = array())
    {
        return new static($data);
    }

    /**
     * @return array
     *
     * @throws \LogicException
     */
    public function toArray()
    {
        $payload = array_filter($this->getPayload(), function($item) {
                return null !== $item;
            });

        if (isset($payload[MessageMeta::META])) {
            throw new \LogicException(sprintf('Key [%s] is reserved.', MessageMeta::META));
        }

        if (isset($payload[MessageActor::ACTOR])) {
            throw new \LogicException(sprintf('Key [%s] is reserved.', MessageActor::ACTOR));
        }

        return array_merge(array(
                MessageMeta::META => $this->meta,
                MessageActor::ACTOR => $this->actor,
            ), $payload);
    }

    /**
     * Default message id is a version 1 uuid.  Override in message
     * class if you desire a different type of uuid.
     *
     * @return Uuid
     */
    protected function generateMessageId()
    {
        return Uuid::uuid1();
    }

    /**
     * Override in your message class.
     *
     * @return array
     */
    protected function getPayload()
    {
        return [];
    }
}