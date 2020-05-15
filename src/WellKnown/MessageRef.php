<?php

namespace Gdbots\Pbj\WellKnown;

use Gdbots\Common\FromArray;
use Gdbots\Common\ToArray;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Exception\InvalidArgumentException;
use Gdbots\Pbj\Exception\LogicException;
use Gdbots\Pbj\SchemaCurie;

/**
 * Represents a reference to a message.  Typically used to link messages
 * together via a correlator or "links".  Format for a reference:
 * vendor:package:category:message:id#tag (tag is optional)
 */
final class MessageRef implements FromArray, ToArray, \JsonSerializable
{
    private SchemaCurie $curie;

    /**
     * Any string matching pattern /^[\w\/\.:-]+$/
     */
    private string $id;
    private ?string $tag = null;

    /**
     * When serialized we store the curie as a string so we can
     * restore the singleton instance upon wakeup.
     */
    private ?string $cs = null;

    /**
     * @param SchemaCurie $curie
     * @param string      $id
     * @param string      $tag The tag will be automatically fixed to a slug-formatted-string.
     *
     * @throws \Throwable
     */
    public function __construct(SchemaCurie $curie, string $id, ?string $tag = null)
    {
        $this->curie = $curie;
        $this->id = trim($id) ?: 'null';
        Assertion::regex($this->id, '/^[\w\/\.:-]+$/', null, 'MessageRef.id');

        if (null !== $tag) {
            $this->tag = strtolower(preg_replace('/[^\w\.-]/', '-', $tag)) ?: null;
        }

        if ($this->curie->isMixin()) {
            throw new LogicException('Mixins cannot be used in a MessageRef.');
        }
    }

    public static function fromArray(array $data = []): self
    {
        if (isset($data['curie'])) {
            $id = isset($data['id']) ? (string)$data['id'] : 'null';
            $tag = isset($data['tag']) ? $data['tag'] : null;
            return new self(SchemaCurie::fromString($data['curie']), $id, $tag);
        }

        throw new InvalidArgumentException('Payload must be a MessageRef type.');
    }

    public function toArray(): array
    {
        if (null !== $this->tag) {
            return ['curie' => $this->curie->toString(), 'id' => $this->id, 'tag' => $this->tag];
        }

        return ['curie' => $this->curie->toString(), 'id' => $this->id];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public static function fromString(string $string): self
    {
        $parts = explode('#', $string, 2);
        $ref = $parts[0];
        $tag = isset($parts[1]) ? $parts[1] : null;

        $parts = explode(':', $ref, 5);
        $id = array_pop($parts);
        $curie = SchemaCurie::fromString(implode(':', $parts));
        return new self($curie, $id, $tag);
    }

    public function toString(): string
    {
        if (null !== $this->tag) {
            return $this->curie->toString() . ':' . $this->id . '#' . $this->tag;
        }

        return $this->curie->toString() . ':' . $this->id;
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function getCurie(): SchemaCurie
    {
        return $this->curie;
    }

    public function hasId(): bool
    {
        return 'null' != $this->id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function hasTag(): bool
    {
        return null !== $this->tag;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function equals(self $other): bool
    {
        return $this->toString() === $other->toString();
    }

    public function __sleep()
    {
        $this->cs = $this->curie->toString();
        return ['cs', 'id', 'tag'];
    }

    public function __wakeup()
    {
        $this->curie = SchemaCurie::fromString($this->cs);
        $this->cs = null;
    }
}
