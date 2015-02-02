<?php

namespace Gdbots\Pbj;

use Gdbots\Common\ToArray;
use Gdbots\Pbj\Exception\FieldAlreadyDefined;
use Gdbots\Pbj\Exception\FieldNotDefined;

class Schema implements ToArray, \JsonSerializable
{
    const PBJ_FIELD_NAME = '_pbj';

    /** @var string */
    private $className;

    /** @var SchemaId */
    private $id;

    /** @var Field[] */
    private $fields = [];

    /** @var Field[] */
    private $requiredFields = [];

    /**
     * @param string $className
     * @param SchemaId $id
     */
    final private function __construct($className, SchemaId $id)
    {
        $this->className = $className;
        $this->id = $id;
        $this->addField(
            FieldBuilder::create(self::PBJ_FIELD_NAME, Type\StringType::create())
                ->required()
                ->pattern(SchemaId::VALID_PATTERN)
                ->withDefault($this->id->toString())
                ->build()
        );

        foreach ($this->defineSchema() as $field) {
            $this->addField($field);
        }
    }

    /**
     * When custom schemas are used you can override this method to inject
     * a set of fixed fields that all messages defined with this schema
     * must have.  By default the only fixed/required field is the "_pbj" field.
     *
     * It may be beneficial to have a set of fields that each "category" of
     * messages should have.  An event, command, request, etc.  Those having
     * an "id" or "microtime" would definitely make sense.
     *
     * @return Field[]
     */
    protected function defineSchema()
    {
        return [];
    }

    /**
     * @param string $className
     * @param SchemaId|string $schemaId
     * @param Field[] $fields
     * @return Schema
     */
    final public static function create($className, $schemaId, array $fields = [])
    {
        $id = $schemaId instanceof SchemaId ? $schemaId : SchemaId::fromString($schemaId);
        Assertion::classExists($className, null, 'className');
        Assertion::allIsInstanceOf($fields, 'Gdbots\Pbj\Field', null, 'fields');

        /** @var Schema $schema */
        $schema = new static($className, $id);
        foreach ($fields as $field) {
            $schema->addField($field);
        }

        return $schema;
    }

    /**
     * @return string
     */
    final public function toString()
    {
        return $this->id->toString();
    }

    /**
     * {@inheritdoc}
     */
    final public function toArray()
    {
        return [
            'id' => $this->id->toString(),
            'class_name' => $this->className,
            'fields' => $this->fields
        ];
    }

    /**
     * @return array
     */
    final public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return string
     */
    final public function __toString()
    {
        return $this->id->toString();
    }

    /**
     * @param Field $field
     * @throws FieldAlreadyDefined
     */
    private function addField(Field $field)
    {
        if ($this->hasField($field->getName())) {
            throw new FieldAlreadyDefined($this, $field->getName());
        }
        $this->fields[$field->getName()] = $field;
        if ($field->isRequired()) {
            $this->requiredFields[$field->getName()] = $field;
        }
    }

    /**
     * @return SchemaId
     */
    final public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    final public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return Field[]
     */
    final public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return Field[]
     */
    final public function getRequiredFields()
    {
        return $this->requiredFields;
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    final public function hasField($fieldName)
    {
        return isset($this->fields[$fieldName]);
    }

    /**
     * @param string $fieldName
     * @return Field
     * @throws FieldNotDefined
     */
    final public function getField($fieldName)
    {
        if (!isset($this->fields[$fieldName])) {
            throw new FieldNotDefined($this, $fieldName);
        }
        return $this->fields[$fieldName];
    }
}
