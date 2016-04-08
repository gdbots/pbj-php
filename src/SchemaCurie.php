<?php

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
 * @see SchemaId
 *
 */
final class SchemaCurie implements \JsonSerializable
{
    /**
     * Regular expression pattern for matching a valid SchemaCurie string.
     * @constant string
     */
    const VALID_PATTERN = '/^([a-z0-9-]+):([a-z0-9\.-]+):([a-z0-9-]+)?:([a-z0-9-]+)$/';

    private static $instances = [];

    /** @var string */
    private $curie;

    /** @var string */
    private $vendor;

    /** @var string */
    private $package;

    /** @var string */
    private $category;

    /** @var string */
    private $message;

    /** @var bool */
    private $isMixin = false;

    /** @var SchemaQName */
    private $qname;

    /**
     * @param string $vendor
     * @param string $package
     * @param string $category
     * @param string $message
     */
    private function __construct($vendor, $package, $category, $message)
    {
        $this->vendor = $vendor;
        $this->package = $package;
        $this->category = $category ?: null;
        $this->message = $message;
        $this->curie = sprintf('%s:%s:%s:%s', $this->vendor, $this->package, $this->category, $this->message);
        $this->isMixin = 'mixin' === $this->category;
        $this->qname = SchemaQName::fromCurie($this);
    }

    /**
     * @param SchemaId $id
     * @return SchemaCurie
     */
    public static function fromId(SchemaId $id)
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
     * @return SchemaCurie
     * @throws InvalidSchemaCurie
     */
    public static function fromString($curie)
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

    /**
     * @return string
     */
    public function toString()
    {
        return $this->curie;
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
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isMixin()
    {
        return $this->isMixin;
    }

    /**
     * @return SchemaQName
     */
    public function getQName()
    {
        return $this->qname;
    }
}
