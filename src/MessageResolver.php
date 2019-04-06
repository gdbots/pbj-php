<?php
declare(strict_types=1);

namespace Gdbots\Pbj;

use Gdbots\Pbj\Exception\MoreThanOneMessageForMixin;
use Gdbots\Pbj\Exception\NoMessageForCurie;
use Gdbots\Pbj\Exception\NoMessageForMixin;
use Gdbots\Pbj\Exception\NoMessageForQName;
use Gdbots\Pbj\Exception\NoMessageForSchemaId;

final class MessageResolver
{
    /**
     * An array of all the available schemas keyed by a curie or curie major.
     * The value is an int used to correlate with the other maps.
     *
     * @var int[]
     */
    private static $curies = [];

    /**
     * An array of all the classes, the numeric key relates to the int
     * from the curies array.
     *
     * @var string[]
     */
    private static $classes = [];

    /**
     * An array of ints keyed by the mixin curie. The value is a list of
     * ints which relate to the curies that are using the mixin.
     *
     * @var array
     */
    private static $mixins = [];

    /**
     * An array of resolved lookups by mixin, keyed by the mixin id with major rev
     *
     * @see SchemaId::getCurieMajor
     *
     * @var Schema[]
     */
    private static $resolvedMixins = [];

    /**
     * An array of resolved lookups by qname.
     *
     * @see SchemaQName
     *
     * @var SchemaCurie[]
     */
    private static $resolvedQnames = [];

    /**
     * An array with the following structure (gdbots/pbjc-php automatically creates this)
     * [
     *     'curies' => [
     *         'vendor:package:category:message' => 1, // int is used to connect other values
     *     ],
     *     'classes' => [
     *         1 => 'Vendor\Package\Category\MessageV1', // 1 refers to the value of the curies entry
     *     ],
     *     'mixins' => [
     *         'gdbots:pbjx:mixin:command:v1' => [1] // 1 refers to the value of the curies entry
     *     ],
     * ]
     *
     * @param array $manifest
     */
    public static function registerManifest(array $manifest): void
    {
        self::$curies = $manifest['curies'] ?? [];
        self::$classes = $manifest['classes'] ?? [];
        self::$mixins = $manifest['mixins'] ?? [];
    }

    /**
     * Returns all of the registed schemas.
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
        if (isset(self::$curies[$key])) {
            return self::$classes[self::$curies[$key]];
        }

        throw new NoMessageForCurie(SchemaCurie::fromString($key));
    }

    /**
     * @param SchemaQName|string $qname
     *
     * @return SchemaCurie
     *
     * @throws NoMessageForQName
     */
    public static function resolveQName($qname): SchemaCurie
    {
        if (!$qname instanceof SchemaQName) {
            $qname = SchemaQName::fromString((string)$qname);
        }

        $key = $qname->toString();

        if (isset(self::$resolvedQnames[$key])) {
            return self::$resolvedQnames[$key];
        }

        $qvendor = $qname->getVendor();
        $qmessage = $qname->getMessage();

        foreach (self::$curies as $curie => $id) {
            list($vendor, $package, $category, $message) = explode(':', $curie);
            if ($qvendor === $vendor && $qmessage === $message) {
                return self::$resolvedQnames[$key] = SchemaCurie::fromString($vendor . ':' . $package . ':' . $category . ':' . $message);
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
        $nextId = count(self::$curies) + 10000;
        self::$curies[$schema->getId()->getCurieMajor()] = $nextId;
        self::$classes[$nextId] = $schema->getClassName();
        foreach ($schema->getMixinIds() as $mixin) {
            unset(self::$resolvedMixins[$mixin]);
            if (!isset(self::$mixins[$mixin])) {
                self::$mixins[$mixin] = [];
            }

            self::$mixins[$mixin][] = $nextId;
        }
    }

    /**
     * Adds a single schema id and class name.
     * @see SchemaId::getCurieMajor
     *
     * @param SchemaId|string $id
     * @param string          $className
     */
    public static function register($id, string $className): void
    {
        @trigger_error(sprintf('"%s" is deprecated. Use "registerManifest" instead.', __CLASS__), E_USER_DEPRECATED);

        if ($id instanceof SchemaId) {
            $id = $id->getCurieMajor();
        }

        $nextId = count(self::$curies) + 20000;
        self::$curies[$id] = $nextId;
        self::$classes[$nextId] = $className;
    }

    /**
     * Registers an array of id => className values to the resolver.
     *
     * @param array $map
     */
    public static function registerMap(array $map)
    {
        @trigger_error(sprintf('"%s" is deprecated. Use "registerManifest" instead.', __CLASS__), E_USER_DEPRECATED);
        $nextId = count(self::$curies) + 30000;
        foreach ($map as $curie => $class) {
            ++$nextId;
            self::$curies[$curie] = $nextId;
            self::$classes[$nextId] = $class;
        }
    }

    /**
     * Return the one schema expected to be using the provided mixin.
     *
     * @param Mixin|string $mixin Mixin or curie major
     *
     * @return Schema
     *
     * @throws MoreThanOneMessageForMixin
     * @throws NoMessageForMixin
     */
    public static function findOneUsingMixin($mixin): Schema
    {
        $schemas = self::findAllUsingMixin($mixin);
        if (1 !== count($schemas)) {
            throw new MoreThanOneMessageForMixin($mixin, $schemas);
        }

        return current($schemas);
    }

    /**
     * Returns an array of Schemas using the provided mixin.
     *
     * @param Mixin|string $mixin Mixin or curie major
     *
     * @return Schema[]
     *
     * @throws NoMessageForMixin
     */
    public static function findAllUsingMixin($mixin): array
    {
        if ($mixin instanceof Mixin) {
            $key = $mixin->getId()->getCurieMajor();
        } else {
            $key = $mixin;
        }

        if (!isset(self::$resolvedMixins[$key])) {
            $schemas = [];
            foreach ((self::$mixins[$key] ?? []) as $id) {
                $schemas[] = self::$classes[$id]::schema();
            }
            self::$resolvedMixins[$key] = $schemas;
        }

        if (empty(self::$resolvedMixins[$key])) {
            throw new NoMessageForMixin($mixin);
        }

        return self::$resolvedMixins[$key];
    }
}
