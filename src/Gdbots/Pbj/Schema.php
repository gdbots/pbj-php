<?php

namespace Gdbots\Pbj;

use Gdbots\Common\ToArray;
use Gdbots\Common\Util\ClassUtils;
use Gdbots\Pbj\Exception\FieldAlreadyDefined;
use Gdbots\Pbj\Exception\FieldNotDefined;
use Gdbots\Pbj\Exception\MixinAlreadyAdded;
use Gdbots\Pbj\Exception\MixinNotDefined;

final class Schema implements ToArray, \JsonSerializable
{
    const PBJ_FIELD_NAME = '_schema';

    /** @var string */
    private $className;

    /** @var string */
    private $classShortName;

    /** @var SchemaId */
    private $id;

    /** @var Field[] */
    private $fields = [];

    /** @var Field[] */
    private $requiredFields = [];

    /** @var Mixin[] */
    private $mixins = [];

    /** @var array */
    private $mixinIds = [];

    /**
     * Fields added by mixins that are okay to be overridden
     * by the schema of the message itself.
     *
     * @var array
     */
    private $overridable = [];

    /**
     * @param SchemaId|string $id
     * @param string $className
     * @param Field[] $fields
     * @param Mixin[] $mixins
     */
    public function __construct($id, $className, array $fields = [], array $mixins = [])
    {
        Assertion::classExists($className, null, 'className');
        Assertion::allIsInstanceOf($fields, 'Gdbots\Pbj\Field', null, 'fields');
        Assertion::allIsInstanceOf($mixins, 'Gdbots\Pbj\Mixin', null, 'mixins');

        $this->id = $id instanceof SchemaId ? $id : SchemaId::fromString($id);
        $this->className = $className;
        $this->classShortName = ClassUtils::getShortName($this->className);

        $this->addField(
            FieldBuilder::create(self::PBJ_FIELD_NAME, Type\StringType::create())
                ->required()
                ->pattern(SchemaId::VALID_PATTERN)
                ->withDefault($this->id->toString())
                ->build()
        );

        foreach ($mixins as $mixin) {
            $this->addMixin($mixin);
        }

        foreach ($fields as $field) {
            $this->addField($field);
        }

        $this->mixinIds = array_keys($this->mixins);
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
            'id' => $this->id,
            'curie' => $this->id->getCurie(),
            'class_name' => $this->className,
            'mixins' => array_map(
                function(Mixin $mixin) {
                    return $mixin->getId();
                },
                array_values($this->mixins)
            ),
            'fields' => $this->fields,
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
     * @throws FieldAlreadyDefined
     */
    private function addField(Field $field)
    {
        $fieldName = $field->getName();

        if ($this->hasField($fieldName) && !isset($this->overridable[$fieldName])) {
            throw new FieldAlreadyDefined($this, $fieldName);
        }

        unset($this->overridable[$fieldName]);
        $this->fields[$fieldName] = $field;

        if ($field->isRequired()) {
            $this->requiredFields[$fieldName] = $field;
        }
    }

    /**
     * @param Mixin $mixin
     * @throws FieldAlreadyDefined
     * @throws MixinAlreadyAdded
     */
    private function addMixin(Mixin $mixin)
    {
        $id = $mixin->getId();
        if (isset($this->mixins[$id->getCurieWithMajorRev()])) {
            throw new MixinAlreadyAdded($this, $this->mixins[$id->getCurieWithMajorRev()], $mixin);
        }

        $this->mixins[$id->getCurieWithMajorRev()] = $mixin;
        foreach ($mixin->getFields() as $field) {
            $fieldName = $field->getName();

            // a mixin cannot override the field of another mixin
            if ($this->hasField($fieldName)) {
                throw new FieldAlreadyDefined($this, $fieldName);
            }

            $this->overridable[$fieldName] = true;
            $this->fields[$fieldName] = $field;
            if ($field->isRequired()) {
                $this->requiredFields[$fieldName] = $field;
            }
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
     * @return MessageCurie
     */
    public function getCurie()
    {
        return $this->id->getCurie();
    }

    /**
     * @see SchemaId::getCurieWithMajorRev
     * @return string
     */
    public function getCurieWithMajorRev()
    {
        return $this->id->getCurieWithMajorRev();
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getClassShortName()
    {
        return $this->classShortName;
    }

    /**
     * Convenience method to return the name of the method that should
     * exist on a handler for this messages with this schema.
     *
     * For example, an ImportUserV1 message would be handled by:
     * SomeHandler::importUserV1(ImportUserV1 $command)
     *
     * @return string
     */
    public function getHandlerMethodName()
    {
        return lcfirst($this->classShortName);
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
     * @throws FieldNotDefined
     */
    public function getField($fieldName)
    {
        if (!isset($this->fields[$fieldName])) {
            throw new FieldNotDefined($this, $fieldName);
        }
        return $this->fields[$fieldName];
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
     * Returns true if the mixin is on this schema.
     * @see SchemaId::getCurieWithMajorRev
     *
     * @param string $mixinId
     * @return bool
     */
    public function hasMixin($mixinId)
    {
        return isset($this->mixins[$mixinId]);
    }

    /**
     * @param string $mixinId
     * @return Mixin
     * @throws MixinNotDefined
     */
    public function getMixin($mixinId)
    {
        if (!isset($this->mixins[$mixinId])) {
            throw new MixinNotDefined($this, $mixinId);
        }
        return $this->mixins[$mixinId];
    }

    /**
     * @return Mixin[]
     */
    public function getMixins()
    {
        return $this->mixins;
    }

    /**
     * Returns an array of curies with the major rev.
     * @see SchemaId::getCurieWithMajorRev
     *
     * @return array
     */
    public function getMixinIds()
    {
        return $this->mixinIds;
    }
}
