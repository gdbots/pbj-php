<?php

namespace Gdbots\Pbj;

use Gdbots\Pbj\Exception\NoMessageForCurie;
use Gdbots\Pbj\Exception\NoMessageForSchemaId;

final class MessageResolver
{
    /**
     * An array of all the available class names keyed by the schema resolver key.
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
        $key = $schemaId->getResolverKey();
        if (isset(self::$resolved[$key])) {
            return self::$resolved[$key];
        }

        if (isset(self::$messages[$key])) {
            $className = self::$messages[$key];
            self::$resolved[$key] = $className;
            return $className;
        }

        $curie = $schemaId->getCurie()->toString();
        if (isset(self::$messages[$curie])) {
            $className = self::$messages[$curie];
            self::$resolved[$key] = $className;
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
     * Adds a single schema to the resolver.  This is used in tests
     * or dynamic message schema creation (not a typical use case).
     *
     * @param Schema $schema
     */
    public static function registerSchema(Schema $schema)
    {
        self::$messages[$schema->getId()->getResolverKey()] = $schema->getClassName();
    }

    /**
     * Adds a single schema resolver key and class name.
     * @see SchemaId::getResolverKey
     *
     * @param SchemaId|string $key
     * @param string $className
     */
    public static function register($key, $className)
    {
        if ($key instanceof SchemaId) {
            $key = $key->getResolverKey();
        }
        self::$messages[(string) $key] = $className;
    }

    /**
     * Registers an array of resolver key => className values to the resolver.
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
