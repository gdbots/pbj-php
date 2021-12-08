<?php
declare(strict_types=1);

namespace Gdbots\Pbj;

use Gdbots\Pbj\Exception\InvalidSchemaId;

/**
 * Schemas have fully qualified names, similar to a "urn".  This is combination of ideas from:
 *
 * Amazon Resource Names (ARNs) and AWS Service Namespaces
 * @link http://docs.aws.amazon.com/general/latest/gr/aws-arns-and-namespaces.html
 *
 * SnowPlow Analytics (Iglu)
 * @link http://snowplowanalytics.com/blog/2014/07/01/iglu-schema-repository-released/
 *
 * @link http://en.wikipedia.org/wiki/CURIE
 *
 * And of course the various package managers like composer, npm, etc.
 *
 * Schema Id Format:
 *  pbj:vendor:package:category:message:version
 *
 * Schema Curie Format:
 *  vendor:package:category:message
 *
 * Schema Curie Major Format:
 *  vendor:package:category:message:v#
 *
 * Schema QName Format:
 *  vendor:message
 *
 * Formats:
 *  VENDOR:   [a-z0-9-]+
 *  PACKAGE:  [a-z0-9\.-]+
 *  CATEGORY: ([a-z0-9-]+)? (clarifies the intent of the message, e.g. command, request, event, response, etc.)
 *  MESSAGE:  [a-z0-9-]+
 *  VERSION:  @see SchemaVersion::VALID_PATTERN
 *
 * Examples of fully qualified schema ids:
 *  pbj:acme:videos:event:video-uploaded:1-0-0
 *  pbj:acme:users:command:register-user:1-1-0
 *  pbj:acme:api.videos:request:get-video:1-0-0
 *
 * The fully qualified schema identifier corresponds to a json schema implementing the Gdbots PBJ Json Schema.
 *
 * The schema id must be resolveable to a php class that should be able to read and write
 * messages with payloads that validate using the json schema.  The target class is ideally
 * major revision specific.  As in GetVideoV1, GetVideoV2, etc.  Only "major" revisions
 * should require a unique class since all other schema changes should not break anything.
 *
 * @see  SchemaVersion
 *
 */
final class SchemaId implements \JsonSerializable
{
    /**
     * Regular expression pattern for matching a valid SchemaId string.
     * @constant string
     */
    const VALID_PATTERN = '/^pbj:([a-z0-9-]+):([a-z0-9\.-]+):([a-z0-9-]+)?:([a-z0-9-]+):([0-9]+-[0-9]+-[0-9]+)$/';

    private static array $instances = [];

    private string $id;

    /**
     * The curie is the short name for the schema (without the version) that can be used
     * to reference another message without fully qualifying the version.
     *
     * @var SchemaCurie
     */
    private SchemaCurie $curie;

    private string $vendor;
    private string $package;
    private ?string $category;
    private string $message;
    private SchemaVersion $version;

    private function __construct(string $vendor, string $package, string $category, string $message, string $version)
    {
        $this->vendor = $vendor;
        $this->package = $package;
        $this->category = $category ?: null;
        $this->message = $message;
        $this->version = SchemaVersion::fromString($version);
        $this->id = sprintf(
            'pbj:%s:%s:%s:%s:%s',
            $this->vendor,
            $this->package,
            $this->category,
            $this->message,
            $this->version->toString()
        );

        $this->curie = SchemaCurie::fromId($this);
    }

    /**
     * @param string $schemaId
     *
     * @return SchemaId
     * @throws InvalidSchemaId
     */
    public static function fromString(string $schemaId): self
    {
        if (isset(self::$instances[$schemaId])) {
            return self::$instances[$schemaId];
        }

        $okay = strlen($schemaId) < 151;
        Assertion::true($okay, 'Schema id cannot be greater than 150 chars.', 'schemaId');
        if (!preg_match(self::VALID_PATTERN, $schemaId, $matches)) {
            throw new InvalidSchemaId(
                sprintf(
                    'Schema id [%s] is invalid.  It must match the pattern [%s].',
                    $schemaId,
                    self::VALID_PATTERN
                )
            );
        }

        self::$instances[$schemaId] = new self($matches[1], $matches[2], $matches[3], $matches[4], $matches[5]);
        return self::$instances[$schemaId];
    }

    public function toString(): string
    {
        return $this->id;
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getVersion(): SchemaVersion
    {
        return $this->version;
    }

    public function getCurie(): SchemaCurie
    {
        return $this->curie;
    }

    /**
     * Returns the major version qualified curie.  This should be used by the MessageResolver,
     * event dispatchers, etc. where consumers will need to be able to reliably type hint or
     * locate classes and provide functionality for a given message, with the expectation
     * that a major revision is likely not compatible with another major revision of the
     * same message.
     *
     * e.g. "vendor:package:category:message:v1"
     */
    public function getCurieMajor(): string
    {
        return $this->curie . ':v' . $this->version->getMajor();
    }

    public function getQName(): SchemaQName
    {
        return $this->curie->getQName();
    }
}
