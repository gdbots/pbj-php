<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\SchemaQName;

class NoMessageForQName extends \LogicException implements GdbotsPbjException
{
    /** @var SchemaQName */
    private $qname;

    /**
     * @param SchemaQName $qname
     */
    public function __construct(SchemaQName $qname)
    {
        $this->qname = $qname;
        parent::__construct(
            sprintf('MessageResolver is unable to resolve [%s] to a SchemaCurie.', $qname->toString())
        );
    }

    /**
     * @return SchemaQName
     */
    public function getQName()
    {
        return $this->qname;
    }
}
