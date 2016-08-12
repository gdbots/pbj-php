<?php

namespace Gdbots\Pbj\WellKnown;

use Gdbots\Common\Util\SlugUtils;
use Gdbots\Common\Util\StringUtils;
use Gdbots\Pbj\Exception\InvalidArgumentException;

abstract class DatedSlugIdentifier implements Identifier, \JsonSerializable
{
    /** @var string */
    protected $slug;

    /**
     * @param string $slug
     * @throws InvalidArgumentException
     */
    protected function __construct($slug)
    {
        if (!is_string($slug)) {
            throw new InvalidArgumentException(
                sprintf('String expected but got [%s].', StringUtils::varToString($slug))
            );
        }

        if (!SlugUtils::isValid($slug, true) || !SlugUtils::containsDate($slug)) {
            throw new InvalidArgumentException(
                sprintf('The value [%s] is not a valid dated slug.', $slug)
            );
        }

        $this->slug = $slug;
    }

    /**
     * @param string $string
     * @param \DateTime $date
     * @return static
     */
    public static function create($string, \DateTime $date = null)
    {
        $slug = SlugUtils::create($string, true);

        if (SlugUtils::containsDate($slug)) {
            return new static($slug);
        }

        $date = $date ?: new \DateTime();
        $slug = SlugUtils::addDate($slug, $date);
        return new static($slug);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    public static function fromString($string)
    {
        return new static($string);
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function equals(Identifier $other)
    {
        return $this == $other;
    }
}

