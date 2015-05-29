<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Mixin;
use Gdbots\Pbj\Schema;

class MoreThanOneMessageForMixin extends \LogicException implements GdbotsPbjException
{
    /** @var Mixin */
    private $mixin;

    /** @var Schema[] */
    private $schemas;

    /**
     * @param Mixin $mixin
     * @param Schema[] $schemas
     */
    public function __construct(Mixin $mixin, array $schemas)
    {
        $this->mixin = $mixin;
        $this->schemas = $schemas;
        $ids = array_map(function(Schema $schema) {
            return $schema->getId()->toString() . ' => ' . $schema->getClassName();
        }, $schemas);
        parent::__construct(
            sprintf(
                'MessageResolver returned multiple messages using [%s] when one was expected.  ' .
                'Messages found:' . PHP_EOL . '%s',
                $mixin->getId()->getCurieWithMajorRev(),
                implode(PHP_EOL, $ids)
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

    /**
     * @return Schema[]
     */
    public function getSchemas()
    {
        return $this->schemas;
    }
}

