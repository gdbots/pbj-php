<?php

namespace Gdbots\Pbj\WellKnown;

use Gdbots\Pbj\Exception\InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class TimeUuidIdentifier extends UuidIdentifier
{
    /**
     * @param UuidInterface|string $uuid
     */
    protected function __construct($uuid)
    {
        if ($uuid instanceof UuidInterface) {
            $version = $uuid->getVersion();
            if ($version !== 1) {
                throw new InvalidArgumentException(
                    sprintf('A time based (version 1) uuid is required.  Version provided [%s].', $version)
                );
            }
        }

        parent::__construct($uuid);
    }

    /**
     * {@inheritdoc}
     * @return static
     */
    public static function generate()
    {
        return new static(Uuid::uuid1());
    }
}
