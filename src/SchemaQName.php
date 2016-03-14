<?php

namespace Gdbots\Pbj;

use Gdbots\Pbj\Exception\InvalidSchemaQName;

/**
 * Schemas can be referenced in an extremely compact manner using a QName.
 * This is NOT 100% reliably unique as the larger your app is the more likely the
 * same message name will be duplicated in another service.
 * @link https://en.wikipedia.org/wiki/QName
 *
 * Schema QName Format:
 *  vendor:message
 *
 * @see SchemaId
 * @see SchemaCurie
 *
 */
final class SchemaQName implements \JsonSerializable
{
    /**
     * Regular expression pattern for matching a valid SchemaQName string.
     * @constant string
     */
    const VALID_PATTERN = '/^([a-z0-9-]+):([a-z0-9-]+)$/';

    private static $instances = [];

    /** @var string */
    private $qname;

    /** @var string */
    private $vendor;

    /** @var string */
    private $message;

    /**
     * @param string $vendor
     * @param string $message
     */
    private function __construct($vendor, $message)
    {
        $this->vendor = $vendor;
        $this->message = $message;
        $this->qname = sprintf('%s:%s', $this->vendor, $this->message);
    }

    /**
     * @param SchemaId $id
     * @return SchemaQName
     */
    public static function fromId(SchemaId $id)
    {
        return self::fromCurie($id->getCurie());
    }

    /**
     * @param SchemaCurie $curie
     * @return SchemaQName
     */
    public static function fromCurie(SchemaCurie $curie)
    {
        $qname = sprintf('%s:%s', $curie->getVendor(), $curie->getMessage());

        if (isset(self::$instances[$qname])) {
            return self::$instances[$qname];
        }

        self::$instances[$qname] = new self($curie->getVendor(), $curie->getMessage());
        return self::$instances[$qname];
    }

    /**
     * @param string $qname
     * @return SchemaQName
     * @throws InvalidSchemaQName
     */
    public static function fromString($qname)
    {
        if (isset(self::$instances[$qname])) {
            return self::$instances[$qname];
        }

        if (!preg_match(self::VALID_PATTERN, $qname, $matches)) {
            throw new InvalidSchemaQName(
                sprintf('SchemaQName [%s] is invalid.  It must match the pattern [%s].', $qname, self::VALID_PATTERN)
            );
        }

        self::$instances[$qname] = new self($matches[1], $matches[2]);
        return self::$instances[$qname];
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->qname;
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
    public function getMessage()
    {
        return $this->message;
    }
}
