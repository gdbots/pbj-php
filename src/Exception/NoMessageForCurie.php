<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\SchemaCurie;

final class NoMessageForCurie extends \LogicException implements GdbotsPbjException
{
    private SchemaCurie $curie;

    public function __construct(SchemaCurie $curie)
    {
        $this->curie = $curie;
        parent::__construct(
            sprintf('MessageResolver is unable to resolve [%s] to a class name.', $curie->toString())
        );
    }

    public function getCurie(): SchemaCurie
    {
        return $this->curie;
    }
}
