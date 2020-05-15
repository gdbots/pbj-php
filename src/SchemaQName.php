<?php
declare(strict_types=1);

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
 * @see  SchemaId
 * @see  SchemaCurie
 *
 */
final class SchemaQName implements \JsonSerializable
{
    /**
     * Regular expression pattern for matching a valid SchemaQName string.
     * @constant string
     */
    const VALID_PATTERN = '/^([a-z0-9-]+):([a-z0-9-]+)$/';

    private static array $instances = [];

    private string $qname;
    private string $vendor;
    private string $message;

    private function __construct(string $vendor, string $message)
    {
        $this->vendor = $vendor;
        $this->message = $message;
        $this->qname = sprintf('%s:%s', $this->vendor, $this->message);
    }

    public static function fromId(SchemaId $id): self
    {
        return self::fromCurie($id->getCurie());
    }

    public static function fromCurie(SchemaCurie $curie): self
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
     *
     * @return self
     *
     * @throws InvalidSchemaQName
     */
    public static function fromString(string $qname): self
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

    public function toString(): string
    {
        return $this->qname;
    }

    public function jsonSerialize()
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

    public function getMessage(): string
    {
        return $this->message;
    }
}
