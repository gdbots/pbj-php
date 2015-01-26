<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\MessageCurie;

class NoMessageForCurieException extends \LogicException implements GdbotsPbjException
{
    /** @var MessageCurie */
    private $curie;

    /**
     * @param MessageCurie $curie
     */
    public function __construct(MessageCurie $curie)
    {
        $this->curie = $curie;
        parent::__construct(
            sprintf('MessageResolver is unable to resolve [%s] to a class name.', $curie->toString())
        );
    }

    /**
     * @return MessageCurie
     */
    public function getCurie()
    {
        return $this->curie;
    }
}

