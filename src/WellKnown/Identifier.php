<?php
declare(strict_types=1);

namespace Gdbots\Pbj\WellKnown;

interface Identifier extends \JsonSerializable
{
    /**
     * Creates an identifier object from a string representation
     *
     * @param string $string
     *
     * @return static
     * @throws \InvalidArgumentException
     */
    public static function fromString(string $string): static;

    /**
     * Returns a string that can be parsed by fromString()
     */
    public function toString(): string;

    /**
     * Returns the message as a human readable string.
     *
     * @return string
     */
    public function __toString();

    /**
     * Compares the object to another Identifier object. Returns true if both have the same type and value.
     *
     * @param Identifier $other
     *
     * @return bool
     */
    public function equals(Identifier $other): bool;
}
