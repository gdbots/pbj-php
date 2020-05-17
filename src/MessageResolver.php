<?php
declare(strict_types=1);

namespace Gdbots\Pbj;

use Gdbots\Pbj\Exception\NoMessageForCurie;
use Gdbots\Pbj\Exception\NoMessageForQName;
use Gdbots\Pbj\Exception\NoMessageForSchemaId;

final class MessageResolver
{
    private static string $defaultVendor = '';

    /**
     * An array of all the available schemas keyed by a curie or curie major.
     *
     * @var Message[]
     */
    private static array $messages = [];

    /**
     * An array of resolved lookups by qname.
     *
     * @see SchemaQName
     *
     * @var SchemaCurie[]
     */
    private static array $resolvedQnames = [];

    /**
     * An array of class names keyed by curie or curie major.
     * [
     *     'vendor:package:category:message' => 'Vendor\Package\Category\MessageV1'
     * ],
     *
     * @param Message[] $messages
     */
    public static function register(array $messages): void
    {
        if (empty(self::$messages)) {
            self::$messages = $messages;
            return;
        }

        self::$messages = array_merge(self::$messages, $messages);
    }

    /**
     * Adds a single schema to the resolver.  This is used in tests or dynamic
     * message schema creation (not a typical or recommended use case).
     *
     * @param Schema $schema
     */
    public static function registerSchema(Schema $schema): void
    {
        self::$messages[$schema->getId()->getCurieMajor()] = $schema->getClassName();
    }

    /**
     * Returns all of the registered messages.
     *
     * @return Message[]
     */
    public static function all(): array
    {
        return array_values(self::$messages);
    }

    /**
     * Returns the fully qualified php class name to be used for the provided schema id.
     *
     * @param SchemaId $id
     *
     * @return Message
     *
     * @throws NoMessageForSchemaId
     */
    public static function resolveId(SchemaId $id): string
    {
        $curieMajor = $id->getCurieMajor();
        if (isset(self::$messages[$curieMajor])) {
            return self::$messages[$curieMajor];
        }

        $curie = $id->getCurie()->toString();
        if (isset(self::$messages[$curie])) {
            return self::$messages[$curie];
        }

        throw new NoMessageForSchemaId($id);
    }

    /**
     * Returns true if the provided curie exists.
     *
     * @param SchemaCurie|string $curie
     *
     * @return bool
     */
    public static function hasCurie($curie): bool
    {
        try {
            self::resolveCurie($curie);
        } catch (NoMessageForCurie $e) {
            return false;
        }

        return true;
    }

    /**
     * Returns the fully qualified php class name to be used for the provided curie.
     *
     * @param SchemaCurie|string $curie
     *
     * @return Message
     *
     * @throws NoMessageForCurie
     */
    public static function resolveCurie($curie): string
    {
        $key = (string)$curie;
        $key = str_replace('*', self::$defaultVendor, $key);
        if (isset(self::$messages[$key])) {
            return self::$messages[$key];
        }

        $v1key = "{$key}:v1";
        if (isset(self::$messages[$v1key])) {
            return self::$messages[$v1key];
        }

        throw new NoMessageForCurie(SchemaCurie::fromString($key));
    }

    /**
     * Returns true if the provided qname exists.
     *
     * @param SchemaQName|string $qname
     *
     * @return bool
     */
    public static function hasQName($qname): bool
    {
        try {
            self::resolveQName($qname);
        } catch (NoMessageForCurie $e) {
            return false;
        } catch (NoMessageForQName $e) {
            return false;
        }

        return true;
    }

    /**
     * @param SchemaQName|string $qname
     *
     * @return Message
     *
     * @throws NoMessageForQName
     */
    public static function resolveQName($qname): string
    {
        if (!$qname instanceof SchemaQName) {
            $qname = str_replace('*', self::$defaultVendor, (string)$qname);
            $qname = SchemaQName::fromString($qname);
        }

        $key = $qname->toString();

        if (isset(self::$resolvedQnames[$key])) {
            return self::resolveCurie(self::$resolvedQnames[$key]);
        }

        $qvendor = $qname->getVendor();
        $qmessage = $qname->getMessage();

        foreach (self::$messages as $curie => $class) {
            [$vendor, $package, $category, $message] = explode(':', $curie);
            if ($qvendor === $vendor && $qmessage === $message) {
                self::$resolvedQnames[$key] = SchemaCurie::fromString("{$vendor}:{$package}:{$category}:{$message}");
                return self::resolveCurie(self::$resolvedQnames[$key]);
            }
        }

        throw new NoMessageForQName($qname);
    }

    /**
     * Resolving a curie or qname can be done without knowing the vendor ahead of time
     * by using an '*' in a (qname) '*:article' or '*:news:node:article' (curie).
     * The '*' will get replaced with the default vendor, .e.g 'acme:article'.
     *
     * @param string $vendor
     */
    public static function setDefaultVendor(string $vendor): void
    {
        self::$defaultVendor = $vendor;
    }
}
