<?php

namespace Gdbots\Pbj;

use Gdbots\Pbj\Exception\InvalidSchemaVersionException;

/**
 * Similar to semantic versioning but with dashes and no "alpha, beta, etc." qualifiers.
 *
 * E.g. 1-0-0.0 (model-revision-addition.patch)
 *
 * MODEL
 * Is incremented when a change is made which breaks the rules of Protobuf/Thrift backward compatibility,
 * such as changing the type of a field.
 *
 * REVISION
 * Is a change which is backward compatible but not forward compatible. Records created from
 * the old version of the schema can be deserialized using the new schema, but not the other way
 * around.  Example: adding a new field to a union type.
 *
 * ADDITION
 * Is a change which is both backward compatible and forward compatible. The previous version of
 * the schema can be used to deserialize records created from the new version of the schema, and
 * vice versa. Example: adding a new optional field.
 *
 * PATCH
 * Is incremented for changes which fix mistakes in the definition of the schema, rather than changes
 * to the model of the data.
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
    const VALID_PATTERN = '/^([0-9]+)-([0-9]+)-([0-9]+)\.([0-9]+)$/';

    /**
     * E.g. 1-0-0.0 (model-revision-addition.patch)
     *
     * @var string
     */
    private $version;

    /** @var int */
    private $model;

    /** @var int */
    private $revision;

    /** @var int */
    private $addition;

    /** @var int */
    private $patch;

    /**
     * @param int $model
     * @param int $revision
     * @param int $addition
     * @param int $patch
     */
    private function __construct($model = 1, $revision = 0, $addition = 0, $patch = 0)
    {
        $this->model = (int) $model;
        $this->revision = (int) $revision;
        $this->addition = (int) $addition;
        $this->patch = (int) $patch;
        $this->version = sprintf('%d-%d-%d.%d', $this->model, $this->revision, $this->addition, $this->patch);
    }

    /**
     * @param string $version   SchemaVersion string, e.g. 1-0-0.0
     * @return SchemaVersion
     * @throws InvalidSchemaVersionException
     */
    public static function fromString($version = '1-0-0.0')
    {
        if (!preg_match(self::VALID_PATTERN, $version, $matches)) {
            throw new InvalidSchemaVersionException(
                sprintf(
                    'Schema version [%s] is invalid.  It must match the pattern [%s].',
                    $version,
                    self::VALID_PATTERN
                )
            );
        }

        return new self($matches[1], $matches[2], $matches[3], $matches[4]);
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
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return int
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * @return int
     */
    public function getAddition()
    {
        return $this->addition;
    }

    /**
     * @return int
     */
    public function getPatch()
    {
        return $this->patch;
    }
}
