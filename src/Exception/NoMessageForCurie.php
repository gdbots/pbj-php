<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\SchemaCurie;

class NoMessageForCurie extends \LogicException implements GdbotsPbjException
{
    /** @var SchemaCurie */
    private $curie;

    /**
     * @param SchemaCurie $curie
     */
    public function __construct(SchemaCurie $curie)
    {
        $this->curie = $curie;
        parent::__construct(
            sprintf('MessageResolver is unable to resolve [%s] to a class name.', $curie->toString())
        );
    }

    /**
     * @return SchemaCurie
     */
    public function getCurie()
    {
        return $this->curie;
    }
}
