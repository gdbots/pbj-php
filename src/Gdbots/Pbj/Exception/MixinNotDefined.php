<?php

namespace Gdbots\Pbj\Exception;

use Gdbots\Pbj\Schema;

class MixinNotDefined extends SchemaException
{
    /** @var string */
    private $mixinId;

    /**
     * @param Schema $schema
     * @param string $mixinId
     */
    public function __construct(Schema $schema, $mixinId)
    {
        $this->schema = $schema;
        $this->mixinId = $mixinId;
        parent::__construct(
            sprintf(
                'Mixin [%s] is not defined on message [%s].',
                $this->mixinId,
                $this->schema->getClassName()
            )
        );
    }

    /**
     * @return string
     */
    public function getMixinId()
    {
        return $this->mixinId;
    }
}
