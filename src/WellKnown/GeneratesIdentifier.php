<?php
declare(strict_types=1);

namespace Gdbots\Pbj\WellKnown;

interface GeneratesIdentifier
{
    /**
     * @return static
     */
    public static function generate(): self;
}
