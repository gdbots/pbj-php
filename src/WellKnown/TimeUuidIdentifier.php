<?php
declare(strict_types=1);

namespace Gdbots\Pbj\WellKnown;

use Gdbots\Pbj\Exception\InvalidArgumentException;
use Ramsey\Uuid\Rfc4122\UuidV1;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class TimeUuidIdentifier extends UuidIdentifier
{
    /**
     * @param UuidInterface|string $uuid
     */
    protected function __construct($uuid)
    {
        if ($uuid instanceof UuidInterface && !($uuid instanceof UuidV1)) {
            throw new InvalidArgumentException(
                sprintf('A time based (version 1) uuid is required.')
            );
        }

        parent::__construct($uuid);
    }

    public static function generate(): self
    {
        return new static(Uuid::uuid1());
    }
}
