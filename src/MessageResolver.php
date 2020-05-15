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
     * The value is an int used to correlate with the other maps.
     *
     * @var int[]
     */
    private static array $curies = [];

    /**
     * An array of all the classes, the numeric key relates to the int
     * from the curies array.
     *
     * @var string[]
     */
    private static array $classes = [];

    /**
     * An array of resolved lookups by qname.
     *
     * @see SchemaQName
     *
     * @var SchemaCurie[]
     */
    private static array $resolvedQnames = [];

    /**
     * An array with the following structure (gdbots/pbjc-php automatically creates this)
     * [
     *     'curies' => [
     *         'vendor:package:category:message' => 1, // int is used to connect other values
     *     ],
     *     'classes' => [
     *         1 => 'Vendor\Package\Category\MessageV1', // 1 refers to the value of the curies entry
     *     ],
     * ]
     *
     * @param array $manifest
     */
    public static function registerManifest(array $manifest): void
    {
        self::$curies = $manifest['curies'] ?? [];
        self::$classes = $manifest['classes'] ?? [];
    }

    /**
     * Returns all of the registered messages.
     *
     * @return Message[]
     */
    public static function all(): array
    {
        return array_values(self::$classes);
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
        if (isset(self::$curies[$curieMajor])) {
            return self::$classes[self::$curies[$curieMajor]];
        }

        $curie = $id->getCurie()->toString();
        if (isset(self::$curies[$curie])) {
            return self::$classes[self::$curies[$curie]];
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
        if (isset(self::$curies[$key])) {
            return self::$classes[self::$curies[$key]];
        }

        throw new NoMessageForCurie(SchemaCurie::fromString($key));
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

        foreach (self::$curies as $curie => $id) {
            [$vendor, $package, $category, $message] = explode(':', $curie);
            if ($qvendor === $vendor && $qmessage === $message) {
                self::$resolvedQnames[$key] = SchemaCurie::fromString("{$vendor}:{$package}:{$category}:{$message}");
                return self::resolveCurie(self::$resolvedQnames[$key]);
            }
        }

        throw new NoMessageForQName($qname);
    }

    /**
     * Adds a single schema to the resolver.  This is used in tests or dynamic
     * message schema creation (not a typical or recommended use case).
     *
     * @param Schema $schema
     */
    public static function registerSchema(Schema $schema): void
    {
        $id = $schema->getId();
        $nextId = count(self::$curies) + 10000;
        self::$curies[$id->getCurieMajor()] = $nextId;
        self::$classes[$nextId] = $schema->getClassName();

        $curie = $id->getCurie()->toString();
        if (isset(self::$curies[$curie])) {
            return;
        }

        ++$nextId;
        self::$curies[$curie] = $nextId;
        self::$classes[$nextId] = $schema->getClassName();
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
