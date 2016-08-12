<?php

namespace Gdbots\Pbj\WellKnown;

interface Identifier
{
    /**
     * Creates an identifier object from a string representation
     *
     * @param string $string
     * @return static
     * @throws \InvalidArgumentException
     */
    public static function fromString($string);

    /**
     * Returns a string that can be parsed by fromString()
     *
     * @return string
     */
    public function toString();

    /**
     * @see Identifier::toString
     * @return string
     */
    public function __toString();

    /**
     * @see Identifier::toString
     * @return string
     */
    public function jsonSerialize();

    /**
     * Compares the object to another Identifier object. Returns true if both have the same type and value.
     *
     * @param Identifier $other
     * @return boolean
     */
    public function equals(Identifier $other);
}
