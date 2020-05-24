<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\SchemaQName;

final class NoMessageForQName extends \LogicException implements GdbotsPbjException
{
    private SchemaQName $qname;

    public function __construct(SchemaQName $qname)
    {
        $this->qname = $qname;
        parent::__construct(
            sprintf('MessageResolver is unable to resolve [%s] to a SchemaCurie.', $qname->toString())
        );
    }

    public function getQName(): SchemaQName
    {
        return $this->qname;
    }
}
