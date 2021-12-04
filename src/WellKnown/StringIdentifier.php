<?php
declare(strict_types=1);

namespace Gdbots\Pbj\WellKnown;

use Gdbots\Pbj\Exception\InvalidArgumentException;

abstract class StringIdentifier implements Identifier
{
    protected string $string;

    protected function __construct(string $string)
    {
        $this->string = trim($string);

        if (empty($this->string)) {
            throw new InvalidArgumentException('String cannot be empty.');
        }
    }

    public static function fromString(string $string): self
    {
        return new static($string);
    }

    public function toString(): string
    {
        return $this->string;
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
