<?php

namespace Gdbots\Pbj\Mixin;

use Gdbots\Pbj\Exception\GdbotsPbjException;
use Gdbots\Pbj\Exception\RequiredFieldNotSet;
use Gdbots\Pbj\Schema;

/**
 * All mixin traits should use this trait to provide the proper methods for all
 * base message operations.  This is mostly for proper type hinting in the IDE
 * but if for some reason you're not extending the AbstractMessage, which does
 * implement these methods, then this would for sure enforce that you do.
 *
 * @method Schema schema
 */
trait MessageTrait
{
    /**
     * @see \Gdbots\Pbj\Message::has
     *
     * @param string $fieldName
     * @return bool
     */
    abstract public function has($fieldName);

    /**
     * @see \Gdbots\Pbj\Message::get
     *
     * @param string $fieldName
     * @return mixed
     */
    abstract public function get($fieldName);

    /**
     * @see \Gdbots\Pbj\Message::clear
     *
     * @param string $fieldName
     * @return static
     *
     * @throws GdbotsPbjException
     * @throws RequiredFieldNotSet
     */
    abstract public function clear($fieldName);

    /**
     * @see \Gdbots\Pbj\Message::setSingleValue
     *
     * @param string $fieldName
     * @param mixed $value
     * @return static
     *
     * @throws GdbotsPbjException
     */
    abstract public function setSingleValue($fieldName, $value);

    /**
     * @see \Gdbots\Pbj\Message::isInSet
     *
     * @param string $fieldName
     * @param mixed $value
     * @return bool
     */
    abstract public function isInSet($fieldName, $value);

    /**
     * @see \Gdbots\Pbj\Message::addToSet
     *
     * @param string $fieldName
     * @param array $values
     * @return static
     *
     * @throws GdbotsPbjException
     */
    abstract public function addToSet($fieldName, array $values);

    /**
     * @see \Gdbots\Pbj\Message::removeFromSet
     *
     * @param string $fieldName
     * @param array $values
     * @return static
     *
     * @throws GdbotsPbjException
     */
    abstract public function removeFromSet($fieldName, array $values);

    /**
     * @see \Gdbots\Pbj\Message::isInList
     *
     * @param string $fieldName
     * @param mixed $value
     * @return bool
     */
    abstract public function isInList($fieldName, $value);

    /**
     * @see \Gdbots\Pbj\Message::getFromListAt
     *
     * @param string $fieldName
     * @param int $index
     * @return mixed
     */
    abstract public function getFromListAt($fieldName, $index);

    /**
     * @see \Gdbots\Pbj\Message::addToList
     *
     * @param string $fieldName
     * @param array $values
     * @return static
     *
     * @throws GdbotsPbjException
     */
    abstract public function addToList($fieldName, array $values);

    /**
     * @see \Gdbots\Pbj\Message::removeFromListAt
     *
     * @param string $fieldName
     * @param int $index
     * @return static
     *
     * @throws GdbotsPbjException
     */
    abstract public function removeFromListAt($fieldName, $index);

    /**
     * @see \Gdbots\Pbj\Message::isInMap
     *
     * @param string $fieldName
     * @param string $key
     * @return bool
     */
    abstract public function isInMap($fieldName, $key);

    /**
     * @see \Gdbots\Pbj\Message::getFromMap
     *
     * @param string $fieldName
     * @param string $key
     * @return mixed
     */
    abstract public function getFromMap($fieldName, $key);

    /**
     * @see \Gdbots\Pbj\Message::addToMap
     *
     * @param string $fieldName
     * @param string $key
     * @param mixed $value
     * @return static
     *
     * @throws GdbotsPbjException
     */
    abstract public function addToMap($fieldName, $key, $value);

    /**
     * @see \Gdbots\Pbj\Message::removeFromMap
     *
     * @param string $fieldName
     * @param string $key
     * @return static
     *
     * @throws GdbotsPbjException
     */
    abstract public function removeFromMap($fieldName, $key);
}
