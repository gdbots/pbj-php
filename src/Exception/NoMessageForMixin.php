<?php

namespace Gdbots\Pbj\Exception;

class NoMessageForMixin extends \LogicException implements GdbotsPbjException
{
    /** @var string */
    private $mixin;

    /**
     * @param string $mixin
     */
    public function __construct(string $mixin)
    {
        $this->mixin = $mixin;
        parent::__construct(sprintf('MessageResolver is unable to find any messages using [%s].', $mixin));
    }

    /**
     * @return string
     */
    public function getMixin(): string
    {
        return $this->mixin;
    }
}
