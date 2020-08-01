<?php
declare(strict_types=1);

namespace Gdbots\Pbj\WellKnown;

use Gdbots\Pbj\Exception\InvalidArgumentException;
use Ramsey\Uuid\Rfc4122\UuidV1;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class TimeUuidIdentifier extends UuidIdentifier
{
    private const VALID_REGEX = '/\A[0-9a-f]{8}-[0-9a-f]{4}-1[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}\z/ms';

    /**
     * @param UuidInterface|string $uuid
     */
    protected function __construct($uuid)
    {
        if ($uuid instanceof UuidInterface && !($uuid instanceof UuidV1)) {
            if (!preg_match(self::VALID_REGEX, $uuid->toString())) {
                throw new InvalidArgumentException(
                    sprintf('A time based (version 1) uuid is required.')
                );
            }
        }

        parent::__construct($uuid);
    }

    public static function generate(): self
    {
        return new static(Uuid::uuid1());
    }
}
