<?php

namespace Gdbots\Pbj;

final class SchemaBuilder
{
    /** @var string */
    private $className;

    /** @var SchemaId */
    private $id;

    /** @var Field[] */
    private $fields = [];

    /** @var MixinId[] */
    private $mixins = [];

    /**
     * @param string $className
     * @param SchemaId $id
     */
    private function __construct($className, SchemaId $id)
    {
        $this->className = $className;
        $this->id = $id;
    }

    /**
     * @param string $className
     * @param SchemaId|string $schemaId
     * @return Schema
     */
    public static function create($className, $schemaId)
    {
        $id = $schemaId instanceof SchemaId ? $schemaId : SchemaId::fromString($schemaId);
        /** @var SchemaBuilder $schema */
        $schema = new static($className, $id);

        return $schema;
    }

    /**
     * @param Field $field
     * @return self
     */
    public function addField(Field $field)
    {
        $this->fields[$field->getName()] = $field;
        return $this;
    }

    /**
     * @param MixinId $mixinId
     * @param Field $field
     * @return self
     */
    public function addMixin(MixinId $mixinId, Field $field)
    {
        $this->fields[$field->getName()] = $field;
        return $this;
    }

    /**
     * @return Schema
     */
    public function build()
    {
        return Schema::create($this->className, $this->id, $this->fields);
    }
}
