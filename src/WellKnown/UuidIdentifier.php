<?php

namespace Gdbots\Pbj\WellKnown;

use Gdbots\Pbj\Assertion;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidIdentifier implements Identifier, GeneratesIdentifier
{
    /** @var string */
    protected $uuid;

    /**
     * @param UuidInterface|string $uuid
     */
    protected function __construct($uuid)
    {
        if (!$uuid instanceof UuidInterface) {
            Assertion::uuid($uuid);
        }

        $this->uuid = (string)$uuid;
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    public static function generate()
    {
        return new static(Uuid::uuid4());
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    public static function fromString($string)
    {
        return new static($string);
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return $this->uuid;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function equals(Identifier $other)
    {
        return $this == $other;
    }
}
