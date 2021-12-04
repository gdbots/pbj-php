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
    private string $version;

    private int $major;
    private int $minor;
    private int $patch;

    private function __construct(int $major = 1, int $minor = 0, int $patch = 0)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->version = sprintf('%d-%d-%d', $this->major, $this->minor, $this->patch);
    }

    /**
     * @param string $version SchemaVersion string, e.g. 1-0-0
     *
     * @return self
     *
     * @throws InvalidSchemaVersion
     */
    public static function fromString(string $version = '1-0-0'): self
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

        return new self((int)$matches[1], (int)$matches[2], (int)$matches[3]);
    }

    public function toString(): string
    {
        return $this->version;
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function getMajor(): int
    {
        return $this->major;
    }

    public function getMinor(): int
    {
        return $this->minor;
    }

    public function getPatch(): int
    {
        return $this->patch;
    }
}
