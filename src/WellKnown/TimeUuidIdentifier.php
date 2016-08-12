<?php

namespace Gdbots\Pbj\WellKnown;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class TimeUuidIdentifier extends UuidIdentifier
{
    /**
     * @param UuidInterface $uuid
     * @throws \InvalidArgumentException
     */
    protected function __construct(UuidInterface $uuid)
    {
        parent::__construct($uuid);
        $version = $uuid->getVersion();
        if ($version !== 1) {
            throw new \InvalidArgumentException(
                sprintf('A time based (version 1) uuid is required.  Version provided [%s].', $version)
            );
        }
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
