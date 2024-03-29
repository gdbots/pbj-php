<?php
declare(strict_types=1);

namespace Gdbots\Pbj\WellKnown;

use Gdbots\Pbj\Assertion;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidIdentifier implements Identifier, GeneratesIdentifier
{
    protected string $uuid;

    protected function __construct(UuidInterface|string $uuid)
    {
        if (!$uuid instanceof UuidInterface) {
            Assertion::uuid($uuid);
        }

        $this->uuid = (string)$uuid;
    }

    public static function generate(): static
    {
        return new static(Uuid::uuid4());
    }

    public static function fromString(string $string): static
    {
        return new static($string);
    }

    public function toString(): string
    {
        return $this->uuid;
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function equals(Identifier $other): bool
    {
        return $this == $other;
    }
}
