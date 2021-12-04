<?php
declare(strict_types=1);

namespace Gdbots\Pbj;

use Gdbots\Pbj\Exception\InvalidSchemaCurie;

/**
 * Schemas can be fully qualified by the schema id (which includes the version)
 * or the short form which is called a CURIE or "compact uri".
 * @link http://en.wikipedia.org/wiki/CURIE
 *
 * Schema Curie Format:
 *  vendor:package:category:message
 *
 * @see  SchemaId
 *
 */
final class SchemaCurie implements \JsonSerializable
{
    /**
     * Regular expression pattern for matching a valid SchemaCurie string.
     * @constant string
     */
    const VALID_PATTERN = '/^([a-z0-9-]+):([a-z0-9\.-]+):([a-z0-9-]+)?:([a-z0-9-]+)$/';

    private static array $instances = [];
    private string $curie;
    private string $vendor;
    private string $package;
    private ?string $category;
    private string $message;
    private bool $isMixin = false;
    private SchemaQName $qname;

    private function __construct(string $vendor, string $package, ?string $category, string $message)
    {
        $this->vendor = $vendor;
        $this->package = $package;
        $this->category = $category ?: null;
        $this->message = $message;
        $this->curie = sprintf('%s:%s:%s:%s', $this->vendor, $this->package, $this->category, $this->message);
        $this->isMixin = 'mixin' === $this->category;
        $this->qname = SchemaQName::fromCurie($this);
    }

    public static function fromId(SchemaId $id): self
    {
        $curie = substr(str_replace(':' . $id->getVersion()->toString(), '', $id->toString()), 4);

        if (isset(self::$instances[$curie])) {
            return self::$instances[$curie];
        }

        self::$instances[$curie] = new self($id->getVendor(), $id->getPackage(), $id->getCategory(), $id->getMessage());
        return self::$instances[$curie];
    }

    /**
     * @param string $curie
     *
     * @return SchemaCurie
     *
     * @throws InvalidSchemaCurie
     */
    public static function fromString(string $curie): self
    {
        if (isset(self::$instances[$curie])) {
            return self::$instances[$curie];
        }

        $okay = strlen($curie) < 146;
        Assertion::true($okay, 'SchemaCurie cannot be greater than 145 chars.', 'curie');
        if (!preg_match(self::VALID_PATTERN, $curie, $matches)) {
            throw new InvalidSchemaCurie(
                sprintf(
                    'SchemaCurie [%s] is invalid.  It must match the pattern [%s].',
                    $curie,
                    self::VALID_PATTERN
                )
            );
        }

        self::$instances[$curie] = new self($matches[1], $matches[2], $matches[3], $matches[4]);
        return self::$instances[$curie];
    }

    public function toString(): string
    {
        return $this->curie;
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

    public function isMixin(): bool
    {
        return $this->isMixin;
    }

    public function getQName(): SchemaQName
    {
        return $this->qname;
    }
}
