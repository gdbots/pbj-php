<?php

namespace Gdbots\Pbj;

use Gdbots\Pbj\Exception\InvalidMixinId;

/**
 * Mixins are schemas that cannot be used by themselves but are mixed into other schemas.
 * This allows for functionality to be shared among many messages.  A mixin implies
 * properties and/or behavior being added to another object.
 *
 * @link http://en.wikipedia.org/wiki/Mixin
 *
 * Mixin Id Format:
 *  pbj:vendor:package:category:name:version
 *
 * Formats:
 *  VENDOR:   [a-z0-9-]+
 *  PACKAGE:  [a-z0-9\.-]+
 *  CATEGORY: mixin (always that literal text)
 *  NAME:     [a-z0-9-]+ (the short/friendly name of the mixin)
 *  VERSION:  @see SchemaVersion::VALID_PATTERN
 *
 * Examples of fully qualified mixin ids:
 *  pbj:acme:videos:mixin:requires-moderation:1-0-0
 *  pbj:acme:users:mixin:tracks-activity:1-1-0
 *  pbj:acme:api.videos:mixin:pageable:1-0-0
 *
 */

final class MixinId implements \JsonSerializable
{
    /**
     * Regular expression pattern for matching a valid MixinId string.
     * @constant string
     */
    const VALID_PATTERN = '/^pbj:([a-z0-9-]+):([a-z0-9\.-]+):mixin:([a-z0-9-]+):([0-9]+-[0-9]+-[0-9]+)$/';

    private static $instances = [];

    /** @var string */
    private $id;

    /**
     * The curie is the short name for the mixin (without the version) that can be used
     * to reference a mixin without fully qualifying the version.
     *
     * @var MessageCurie
     */
    private $curie;

    /** @var string */
    private $vendor;

    /** @var string */
    private $package;

    /** @var string */
    private $name;

    /** @var SchemaVersion */
    private $version;

    /**
     * @param string $vendor
     * @param string $package
     * @param string $name
     * @param string $version
     */
    private function __construct($vendor, $package, $name, $version)
    {
        $this->vendor = $vendor;
        $this->package = $package;
        $this->name = $name;
        $this->version = SchemaVersion::fromString($version);
        $this->id = sprintf(
            'pbj:%s:%s:mixin:%s:%s',
            $this->vendor,
            $this->package,
            $this->name,
            $this->version->toString()
        );
        $this->curie = MessageCurie::fromMixinId($this);
    }

    /**
     * @param string $mixinId
     * @return MixinId
     * @throws InvalidMixinId
     */
    public static function fromString($mixinId)
    {
        if (isset(self::$instances[$mixinId])) {
            return self::$instances[$mixinId];
        }

        $okay = strlen($mixinId) < 151;
        Assertion::true($okay, 'Mixin id cannot be greater than 150 chars.', 'mixinId');
        if (!preg_match(self::VALID_PATTERN, $mixinId, $matches)) {
            throw new InvalidMixinId(
                sprintf(
                    'Mixin id [%s] is invalid.  It must match the pattern [%s].',
                    $mixinId,
                    self::VALID_PATTERN
                )
            );
        }

        self::$instances[$mixinId] = new self($matches[1], $matches[2], $matches[3], $matches[4]);
        return self::$instances[$mixinId];
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->toString();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return string
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @return string
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return SchemaVersion
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return MessageCurie
     */
    public function getCurie()
    {
        return $this->curie;
    }

    /**
     * Returns the string applications should use when binding events or checking
     * if a message has a mixin.
     *
     * e.g. "vendor:package:mixin:name:v1"
     *
     * @return string
     */
    public function getResolverKey()
    {
        return $this->curie . ':v' . $this->version->getMajor();
    }
}
