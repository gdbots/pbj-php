<?php
declare(strict_types=1);

namespace Gdbots\Pbj\WellKnown;

interface GeneratesIdentifier
{
    public static function generate(): static;
}
