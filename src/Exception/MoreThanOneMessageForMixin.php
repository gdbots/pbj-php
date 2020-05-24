<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Exception;

final class MoreThanOneMessageForMixin extends \LogicException implements GdbotsPbjException
{
    private string $mixin;
    private array $curies;

    public function __construct(string $mixin, array $curies)
    {
        $this->mixin = $mixin;
        $this->curies = $curies;
        parent::__construct(
            sprintf(
                'MessageResolver returned multiple curies using [%s] when one was expected.  ' .
                'Curies found:' . PHP_EOL . '%s',
                $mixin,
                implode(PHP_EOL, $this->curies)
            )
        );
    }

    public function getMixin(): string
    {
        return $this->mixin;
    }

    public function getCuries(): array
    {
        return $this->curies;
    }
}

