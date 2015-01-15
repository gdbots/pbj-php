<?php

namespace Gdbots\Pbj;

use Gdbots\Pbj\Exception\FieldAlreadyDefinedException;
use Gdbots\Pbj\Exception\FieldNotDefinedException;

// todo: implement toArray and JsonSerializable
final class Schema
{
    const CURIE_FIELD_NAME = '_curie';
    const VERSION_FIELD_NAME = '_sv';

    /** @var string */
    private $className;

    /** @var SchemaVersion */
    private $version;

    /** @var Field[] */
    private $fields = [];

    /** @var Field[] */
    private $requiredFields = [];

    /**
     * @param string $className
     * @param SchemaVersion $version
     */
    private function __construct($className, SchemaVersion $version)
    {
        $this->className = $className;
        $this->version = $version;

        // todo: add fixed fields
    }

    /**
     * @param string $className
     * @param string $version
     * @param Field[] $fields
     * @return Schema
     */
    public static function create($className, $version = '1-0-0', array $fields = [])
    {
        $version = SchemaVersion::fromString($version);
        Assertion::string($className, null, 'className');
        Assertion::allIsInstanceOf($fields, 'Gdbots\Pbj\Field', null, 'fields');

        $schema = new self($className, $version);
        foreach ($fields as $field) {
            $schema->addField($field);
        }

        return $schema;
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
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return SchemaVersion
     */
    public function getVersion()
    {
        return $this->version;
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
