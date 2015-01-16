<?php

namespace Gdbots\Pbj;

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
 * Examples of fully qualified schema ids:
 *  acme:videos:event:video-uploaded:1-0-0.0
 *  acme:users:comment:register-user:1-1-0.2
 *  acme:api.videos:request:get-video:1-0-0.0
 *
 * The fully qualified schema identifier corresponds to a json schema implementing
 * the Gdbots PBJ Json Schema.
 *
 * The schema id must be resolveable to a php class that should be able to read and write
 * messages with payloads that validate using the json schema.
 *
 * Message Resolvers MUST be able to map a schema to a class and a class to a schema id.
 *
 * @see SchemaVersion
 *
 */

// todo: implement toArray and JsonSerializable
final class SchemaId
{
    /** @var string */
    private $vendor;

    /** @var string */
    private $package;

    /** @var string */
    private $type;

    /** @var SchemaVersion */
    private $version;

    /**
     * @param string $className
     * @param SchemaVersion $version
     */
    private function __construct($className, SchemaVersion $version)
    {
    }

    /**
     * @param string $schemaId
     * @return SchemaId
     */
    public static function fromString($schemaId)
    {
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '';
    }

    /**
     * @return SchemaVersion
     */
    public function getVersion()
    {
        return $this->version;
    }
}
