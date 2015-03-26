<?php

namespace Gdbots\Pbj;

use Gdbots\Common\FromArray;
use Gdbots\Common\ToArray;
use Gdbots\Common\Util\SlugUtils;
use Gdbots\Identifiers\UuidIdentifier;
use Gdbots\Pbj\Exception\InvalidArgumentException;
use Gdbots\Pbj\Exception\LogicException;

/**
 * Represents a reference to a message.  Typically used to link messages
 * together via a correlator or "links".  Format for a reference:
 * vendor:package:category:message:uuid#tag (tag is optional)
 */
final class MessageRef implements FromArray, ToArray, \JsonSerializable
{
    /** @var MessageCurie */
    private $curie;

    /** @var UuidIdentifier */
    private $id;

    /** @var string */
    private $tag;

    /**
     * @param MessageCurie $curie
     * @param UuidIdentifier $id
     * @param string $tag The tag will be automatically fixed to a slug-formatted-string.
     * @throws LogicException
     */
    public function __construct(MessageCurie $curie, UuidIdentifier $id, $tag = null)
    {
        $this->curie = $curie;
        $this->id = $id;
        if (null !== $tag) {
            $this->tag = SlugUtils::create($tag) ?: null;
        }

        if ($this->curie->isMixin()) {
            throw new LogicException('Mixins cannot be used in a MessageRef.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function fromArray(array $data = [])
    {
        if (isset($data['curie'])) {
            $tag = isset($data['tag']) ? $data['tag'] : null;
            return new self(
                MessageCurie::fromString($data['curie']),
                UuidIdentifier::fromString($data['id']),
                $tag
            );
        }
        throw new InvalidArgumentException('Payload must be a MessageRef type.');
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        if (null !== $this->tag) {
            return [
                'curie' => $this->curie->toString(),
                'id' => $this->id->toString(),
                'tag' => $this->tag
            ];
        }
        return ['curie' => $this->curie->toString(), 'id' => $this->id->toString()];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param string $string A string with format curie:uuid#tag
     * @return self
     */
    public static function fromString($string)
    {
        list($ref, $tag) = explode('#', $string, 2);
        $parts = explode(':', $ref);
        $id = UuidIdentifier::fromString(array_pop($parts));
        $curie = MessageCurie::fromString(implode(':', $parts));
        return new self($curie, $id, $tag);
    }

    /**
     * @return string
     */
    public function toString()
    {
        if (null !== $this->tag) {
            return $this->curie->toString() . ':' . $this->id->toString() . '#' . $this->tag;
        }
        return $this->curie->toString() . ':' . $this->id->toString();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return MessageCurie
     */
    public function getCurie()
    {
        return $this->curie;
    }

    /**
     * @return UuidIdentifier
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function hasTag()
    {
        return null !== $this->tag;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param MessageRef $other
     * @return bool
     */
    public function equals(MessageRef $other)
    {
        return $this->toString() === $other->toString();
    }
}
