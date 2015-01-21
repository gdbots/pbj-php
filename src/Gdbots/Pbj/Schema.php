<?php

namespace Gdbots\Pbj;

use Gdbots\Common\ToArray;
use Gdbots\Pbj\Exception\FieldAlreadyDefinedException;
use Gdbots\Pbj\Exception\FieldNotDefinedException;

final class Schema implements ToArray, \JsonSerializable
{
    const FIELD_NAME = '_pbj';

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
    private function __construct($className, SchemaId $id)
    {
        $this->className = $className;
        $this->id = $id;
        $this->addField(
            FieldBuilder::create(self::FIELD_NAME, Type\StringType::create())
                ->required()
                ->pattern(SchemaId::VALID_PATTERN)
                ->withDefault($this->id->toString())
                ->build()
        );
    }

    /**
     * @param string $className
     * @param SchemaId|string $schemaId
     * @param Field[] $fields
     * @return Schema
     */
    public static function create($className, $schemaId, array $fields = [])
    {
        $id = $schemaId instanceof SchemaId ? $schemaId : SchemaId::fromString($schemaId);
        Assertion::classExists($className, null, 'className');
        Assertion::allIsInstanceOf($fields, 'Gdbots\Pbj\Field', null, 'fields');

        $schema = new self($className, $id);
        foreach ($fields as $field) {
            $schema->addField($field);
        }

        return $schema;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->id->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
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
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id->toString();
    }

    /**
     * @param Field $field
     * @throws FieldAlreadyDefinedException
     */
    private function addField(Field $field)
    {
        if ($this->hasField($field->getName())) {
            throw new FieldAlreadyDefinedException($this, $field->getName());
        }
        $this->fields[$field->getName()] = $field;
        if ($field->isRequired()) {
            $this->requiredFields[$field->getName()] = $field;
        }
    }

    /**
     * @return SchemaId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return Field[]
     */
    public function getRequiredFields()
    {
        return $this->requiredFields;
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function hasField($fieldName)
    {
        return isset($this->fields[$fieldName]);
    }

    /**
     * @param string $fieldName
     * @return Field
     * @throws FieldNotDefinedException
     */
    public function getField($fieldName)
    {
        if (!isset($this->fields[$fieldName])) {
            throw new FieldNotDefinedException($this, $fieldName);
        }
        return $this->fields[$fieldName];
    }
}
