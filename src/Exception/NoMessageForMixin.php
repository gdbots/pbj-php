<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Mixin;

class NoMessageForMixin extends \LogicException implements GdbotsPbjException
{
    /** @var Mixin */
    private $mixin;

    /**
     * @param Mixin $mixin
     */
    public function __construct(Mixin $mixin)
    {
        $this->mixin = $mixin;
        parent::__construct(
            sprintf(
                'MessageResolver is unable to find any messages using [%s].',
                $mixin->getId()->getCurieWithMajorRev()
            )
        );
    }

    /**
     * @return Mixin
     */
    public function getMixin()
    {
        return $this->mixin;
    }
}

