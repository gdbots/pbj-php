<?php
declare(strict_types=1);

namespace Gdbots\Pbj\WellKnown;

use Gdbots\Pbj\Exception\InvalidArgumentException;
use Gdbots\Pbj\Util\SlugUtil;

abstract class DatedSlugIdentifier implements Identifier
{
    protected string $slug;

    protected function __construct(string $slug)
    {
        if (!SlugUtil::isValid($slug, true) || !SlugUtil::containsDate($slug)) {
            throw new InvalidArgumentException(
                sprintf('The value [%s] is not a valid dated slug.', $slug)
            );
        }

        $this->slug = $slug;
    }

    /**
     * @param string             $string
     * @param \DateTimeInterface $date
     *
     * @return static
     */
    public static function create(string $string, ?\DateTimeInterface $date = null): self
    {
        $slug = SlugUtil::create($string, true);

        if (SlugUtil::containsDate($slug)) {
            return new static($slug);
        }

        $date = $date ?: new \DateTime();
        $slug = SlugUtil::addDate($slug, $date);
        return new static($slug);
    }

    public static function fromString(string $string): self
    {
        return new static($string);
    }

    public function toString(): string
    {
        return $this->slug;
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function equals(Identifier $other): bool
    {
        return $this == $other;
    }
}

