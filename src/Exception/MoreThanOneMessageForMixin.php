<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Schema;

class MoreThanOneMessageForMixin extends \LogicException implements GdbotsPbjException
{
    /** @var string */
    private $mixin;

    /** @var Schema[] */
    private $schemas;

    /**
     * @param string   $mixin
     * @param Schema[] $schemas
     */
    public function __construct(string $mixin, array $schemas)
    {
        $this->mixin = $mixin;
        $this->schemas = $schemas;
        $ids = array_map(function (Schema $schema) {
            return $schema->getId()->toString() . ' => ' . $schema->getClassName();
        }, $schemas);
        parent::__construct(
            sprintf(
                'MessageResolver returned multiple messages using [%s] when one was expected.  ' .
                'Messages found:' . PHP_EOL . '%s',
                $mixin,
                implode(PHP_EOL, $ids)
            )
        );
    }

    /**
     * @return string
     */
    public function getMixin(): string
    {
        return $this->mixin;
    }

    /**
     * @return Schema[]
     */
    public function getSchemas(): array
    {
        return $this->schemas;
    }
}

