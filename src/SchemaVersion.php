<?php
declare(strict_types=1);

namespace Gdbots\Pbj;

use Gdbots\Pbj\Exception\InvalidSchemaVersion;

/**
 * Similar to semantic versioning but with dashes and no "alpha, beta, etc." qualifiers.
 *
 * E.g. 1-0-0 (major-minor-patch)
 *
 * MAJOR
 * Is incremented when a change is made which breaks the rules of Protobuf/Thrift backward compatibility,
 * such as changing the type of a field.
 *
 * MINOR
 * Is a change which is backward compatible but not forward compatible. Records created from
 * the old version of the schema can be deserialized using the new schema, but not the other way
 * around.  Example: adding a new field to a union type.
 *
 * PATCH
 * Is a change which is both backward compatible and forward compatible. The previous version of
 * the schema can be used to deserialize records created from the new version of the schema, and
 * vice versa. Example: adding a new optional field.
 *
 * @link http://semver.org/
 * @link http://snowplowanalytics.com/blog/2014/05/13/introducing-schemaver-for-semantic-versioning-of-schemas/
 *
 */

final class SchemaVersion implements \JsonSerializable
{
    /**
     * Regular expression pattern for matching a valid SchemaVersion string.
     * @constant string
     */
    const VALID_PATTERN = '/^([0-9]+)-([0-9]+)-([0-9]+)$/';

    /**
     * E.g. 1-0-0 (major-minor-patch)
     *
     * @var string
     */
    private $version;

    /** @var int */
    private $major;

    /** @var int */
    private $minor;

    /** @var int */
    private $patch;

    /**
     * @param int $major
     * @param int $minor
     * @param int $patch
     */
    private function __construct($major = 1, $minor = 0, $patch = 0)
    {
        $this->major = (int) $major;
        $this->minor = (int) $minor;
        $this->patch = (int) $patch;
        $this->version = sprintf('%d-%d-%d', $this->major, $this->minor, $this->patch);
    }

    /**
     * @param string $version   SchemaVersion string, e.g. 1-0-0
     * @return SchemaVersion
     * @throws InvalidSchemaVersion
     */
    public static function fromString($version = '1-0-0')
    {
        if (!preg_match(self::VALID_PATTERN, $version, $matches)) {
            throw new InvalidSchemaVersion(
                sprintf(
                    'Schema version [%s] is invalid.  It must match the pattern [%s].',
                    $version,
                    self::VALID_PATTERN
                )
            );
        }

        return new self($matches[1], $matches[2], $matches[3]);
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->version;
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
     * @return int
     */
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * @return int
     */
    public function getMinor()
    {
        return $this->minor;
    }

    /**
     * @return int
     */
    public function getPatch()
    {
        return $this->patch;
    }
}
