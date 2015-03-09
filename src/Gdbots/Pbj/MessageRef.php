<?php

namespace Gdbots\Pbj;

use Gdbots\Common\FromArray;
use Gdbots\Common\ToArray;
use Gdbots\Identifiers\UuidIdentifier;

/**
 * Represents a reference to a message.  Typically used to link messages to
 * each as a correlator or "links".
 */
final class MessageRef implements FromArray, ToArray, \JsonSerializable
{
    /** @var MessageCurie */
    private $curie;

    /** @var UuidIdentifier */
    private $id;

    /**
     * @param MessageCurie $curie
     * @param UuidIdentifier $id
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(MessageCurie $curie, UuidIdentifier $id)
    {
        $this->curie = $curie;
        $this->id = $id;
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
     * {@inheritdoc}
     */
    public static function fromArray(array $data = [])
    {
        if (isset($data['curie'])) {
            /** @var UuidIdentifier $id */
            $id = UuidIdentifier::fromString($data['id']);
            return new self(MessageCurie::fromString($data['curie']), $id);
        }
        throw new \InvalidArgumentException('Payload must be a MessageRef type.');
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
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
     * @param string $string A string with format curie:id
     * @return self
     */
    public static function fromString($string)
    {
        $parts = explode(':', $string);
        /** @var UuidIdentifier $id */
        $id = UuidIdentifier::fromString(array_pop($parts));
        $curie = MessageCurie::fromString(implode(':', $parts));
        return new self($curie, $id);
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->curie->toString() . ':' . $this->id->toString();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
