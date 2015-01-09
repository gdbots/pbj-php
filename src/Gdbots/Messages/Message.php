<?php

namespace Gdbots\Messages;

interface Message
{
    /**
     * @return Field[]
     * @throws \LogicException
     */
    public static function fields();

    /**
     * @param string $name
     * @return Field
     * @throws \InvalidArgumentException
     */
    public static function field($name);

    /**
     * Performs the same function as "fromArray"
     *
     * @param array $data
     * @return static
     */
    public static function create(array $data = []);

    /**
     * Returns a new message from the provided array.  The array
     * should be data returned from toArray or at least match
     * that signature.
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data = []);

    /**
     * Returns the message as an associative array.
     *
     * @return array
     */
    public function toArray();

    /**
     * Return the value for the given field.
     *
     * @param Field|string $nameOrField
     * @return mixed
     */
    public function get($nameOrField);
}