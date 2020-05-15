<?php
declare(strict_types=1);

namespace Gdbots\Pbj\WellKnown;

use Gdbots\Common\Util\SlugUtils;
use Gdbots\Pbj\Exception\InvalidArgumentException;

abstract class SlugIdentifier implements Identifier
{
    protected string $slug;

    protected function __construct(string $slug)
    {
        if (!SlugUtils::isValid($slug)) {
            throw new InvalidArgumentException(
                sprintf('The value [%s] is not a valid slug.', $slug)
            );
        }

        $this->slug = $slug;
    }

    public static function create(string $string): self
    {
        return new static(SlugUtils::create($string));
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

    public function jsonSerialize()
    {
        return $this->toString();
    }

    public function equals(Identifier $other): bool
    {
        return $this == $other;
    }
}
