<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Exception;

final class NoMessageForMixin extends \LogicException implements GdbotsPbjException
{
    private string $mixin;

    public function __construct(string $mixin)
    {
        $this->mixin = $mixin;
        parent::__construct(sprintf('MessageResolver is unable to find any messages using [%s].', $mixin));
    }

    public function getMixin(): string
    {
        return $this->mixin;
    }
}
