<?php

namespace Gdbots\Pbj;

use Gdbots\Pbj\Exception\NoMessageForCurie;
use Gdbots\Pbj\Exception\NoMessageForSchemaId;

final class MessageResolver
{
    /**
     * An array of all the available class names keyed by the schema resolver key
     * and curies for resolution that is not version specific.
     *
     * @var array
     */
    private static $messages = [];

    /**
     * An array of resolved messages in this request.
     * @var array
     */
    private static $resolved = [];

    /**
     * Returns the fully qualified php class name to be used for the provided schema id.
     *
     * @param SchemaId $schemaId
     * @return string
     * @throws NoMessageForSchemaId
     */
    public static function resolveSchemaId(SchemaId $schemaId)
    {
        $curieMajor = $schemaId->getCurieWithMajorRev();
        if (isset(self::$resolved[$curieMajor])) {
            return self::$resolved[$curieMajor];
        }

        if (isset(self::$messages[$curieMajor])) {
            $className = self::$messages[$curieMajor];
            self::$resolved[$curieMajor] = $className;
            return $className;
        }

        $curie = $schemaId->getCurie()->toString();
        if (isset(self::$messages[$curie])) {
            $className = self::$messages[$curie];
            self::$resolved[$curieMajor] = $className;
            self::$resolved[$curie] = $className;
            return $className;
        }

        throw new NoMessageForSchemaId($schemaId);
    }

    /**
     * Returns the fully qualified php class name to be used for the provided curie.
     *
     * @param MessageCurie $curie
     * @return string
     * @throws NoMessageForCurie
     */
    public static function resolveMessageCurie(MessageCurie $curie)
    {
        $key = $curie->toString();
        if (isset(self::$resolved[$key])) {
            return self::$resolved[$key];
        }

        if (isset(self::$messages[$key])) {
            $className = self::$messages[$key];
            self::$resolved[$key] = $className;
            return $className;
        }

        throw new NoMessageForCurie($curie);
    }

    /**
     * Adds a single schema to the resolver.  This is used in tests or dynamic
     * message schema creation (not a typical use case).
     *
     * @param Schema $schema
     */
    public static function registerSchema(Schema $schema)
    {
        self::$messages[$schema->getId()->getCurieWithMajorRev()] = $schema->getClassName();
    }

    /**
     * Adds a single schema id and class name.
     * @see SchemaId::getCurieWithMajorRev
     *
     * @param SchemaId|string $id
     * @param string $className
     */
    public static function register($id, $className)
    {
        if ($id instanceof SchemaId) {
            $id = $id->getCurieWithMajorRev();
        }
        self::$messages[(string) $id] = $className;
    }

    /**
     * Registers an array of id => className values to the resolver.
     *
     * @param array $map
     */
    public static function registerMap(array $map)
    {
        if (empty(self::$messages)) {
            self::$messages = $map;
            return;
        }
        self::$messages = array_merge(self::$messages, $map);
    }
}
