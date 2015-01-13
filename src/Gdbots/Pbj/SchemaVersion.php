<?php

namespace Gdbots\Pbj;

final class SchemaVersion implements \JsonSerializable
{
    private static $instances = [];

    /**
     * A string representing the schema version.  Similar to semantic versioning
     * but with dashes and no "alpha, beta, etc." qualifiers.
     *
     * @link http://semver.org/
     * @link http://snowplowanalytics.com/blog/2014/05/13/introducing-schemaver-for-semantic-versioning-of-schemas/
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
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->version = sprintf('%d-%d-%d', $this->major, $this->minor, $this->patch);
    }

    /**
     * @param string $version   SchemaVersion string, e.g. 1-0-0
     * @return SchemaVersion
     */
    public static function fromString($version = '1-0-0')
    {
        Assertion::regex($version, '/^\d{1,3}-\d{1,3}-\d{1,3}$/', null, 'version');
        if (!isset(self::$instances[$version])) {
            list($major, $minor, $patch) = explode('-', $version);
            $major = (int) $major;
            $minor = (int) $minor;
            $patch = (int) $patch;
            self::$instances[$version] = new self($major, $minor, $patch);
        }
        return self::$instances[$version];
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
        return $this->version;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->version;
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
