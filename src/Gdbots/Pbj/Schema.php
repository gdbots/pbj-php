<?php

namespace Gdbots\Pbj;

use Gdbots\Pbj\Exception\FieldAlreadyDefinedException;
use Gdbots\Pbj\Exception\FieldNotDefinedException;

/**
 * Schemas have fully qualified names, similar to a "urn".  This is combination of ideas from:
 *
 * Amazon Resource Names (ARNs) and AWS Service Namespaces
 * @link http://docs.aws.amazon.com/general/latest/gr/aws-arns-and-namespaces.html
 *
 * SnowPlow Analytics (Iglu)
 * @link http://snowplowanalytics.com/blog/2014/07/01/iglu-schema-repository-released/
 *
 * @link http://en.wikipedia.org/wiki/CURIE
 *
 * And of course the various package managers like composer, npm, etc.
 *
 * Examples of fully qualified schema ids:
 *  acme:videos:event:video-uploaded:1-0-0.0
 *  acme:users:comment:register-user:1-1-0.2
 *  acme:api.videos:request:get-video:1-0-0.0
 *
 * The fully qualified schema identifier corresponds to a json schema implementing
 * the Gdbots PBJ Json Schema.
 *
 * The schema id must be resolveable to a php class that should be able to read and write
 * messages with payloads that validate using the json schema.
 *
 * Message Resolvers MUST be able to map a schema to a class and a class to a schema id.
 *
 * @see SchemaVersion
 *
 */

// todo: implement toArray and JsonSerializable
final class Schema
{
    const FIELD_NAME = '_schema';

    /** @var string */
    private $vendor;

    /** @var string */
    private $package;

    /** @var string */
    private $type;

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

        // todo: add fixed fields and regex pattern for assertion on schema field
        $this->addField(
            FieldBuilder::create(self::FIELD_NAME, Type\String::create())
                ->required()
                //->withDefault($this->getKey())
                ->build()
        );
    }

    /**
     * @param string $className
     * @param SchemaVersion|string $version
     * @param Field[] $fields
     * @return Schema
     */
    public static function create($className, $version = '1-0-0.0', array $fields = [])
    {
        $version = $version instanceof SchemaVersion ? $version : SchemaVersion::fromString($version);
        Assertion::string($className, null, 'className');
        Assertion::allIsInstanceOf($fields, 'Gdbots\Pbj\Field', null, 'fields');

        $schema = new self($className, $version);
        foreach ($fields as $field) {
            $schema->addField($field);
        }

        return $schema;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getKey();
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
    public function getKey()
    {
        return $this->className . ':' . $this->version;
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
