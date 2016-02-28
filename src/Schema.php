<?php

namespace Gdbots\Pbj;

use Gdbots\Common\ToArray;
use Gdbots\Common\Util\ClassUtils;
use Gdbots\Pbj\Exception\FieldAlreadyDefined;
use Gdbots\Pbj\Exception\FieldNotDefined;
use Gdbots\Pbj\Exception\FieldOverrideNotCompatible;
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

    /** @var Mixin[] */
    private $mixinsByCurie = [];

    /** @var array */
    private $mixinIds = [];

    /** @var array */
    private $mixinCuries = [];

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
        $this->mixinCuries = array_keys($this->mixinsByCurie);
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
     * @throws FieldOverrideNotCompatible
     */
    private function addField(Field $field)
    {
        $fieldName = $field->getName();
        if ($this->hasField($fieldName)) {
            $existingField = $this->getField($fieldName);
            if (!$existingField->isOverridable()) {
                throw new FieldAlreadyDefined($this, $fieldName);
            }

            if (!$existingField->isCompatibleForOverride($field)) {
                throw new FieldOverrideNotCompatible($this, $fieldName, $field);
            }
        }

        $this->fields[$fieldName] = $field;
        if ($field->isRequired()) {
            $this->requiredFields[$fieldName] = $field;
        }
    }

    /**
     * @param Mixin $mixin
     * @throws MixinAlreadyAdded
     */
    private function addMixin(Mixin $mixin)
    {
        $id = $mixin->getId();
        $curieStr = $id->getCurie()->toString();

        if (isset($this->mixinsByCurie[$curieStr])) {
            throw new MixinAlreadyAdded($this, $this->mixinsByCurie[$curieStr], $mixin);
        }

        $this->mixins[$id->getCurieWithMajorRev()] = $mixin;
        $this->mixinsByCurie[$curieStr] = $mixin;

        foreach ($mixin->getFields() as $field) {
            $this->addField($field);
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
     * exist to handle this message.
     *
     * For example, an ImportUserV1 message would be handled by:
     * SomeClass::importUserV1(ImportUserV1 $command)
     *
     * @param bool $withMajorRev
     * @return string
     */
    public function getHandlerMethodName($withMajorRev = true)
    {
        if (true === $withMajorRev) {
            return lcfirst($this->classShortName);
        }

        return lcfirst(str_replace('V'.$this->id->getVersion()->getMajor(), '', $this->classShortName));
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
     * Returns true if the mixin is on this schema.  Id provided can be
     * qualified to major rev or just the curie.
     * @see SchemaId::getCurieWithMajorRev
     *
     * @param string $mixinId
     * @return bool
     */
    public function hasMixin($mixinId)
    {
        return isset($this->mixins[$mixinId]) || isset($this->mixinsByCurie[$mixinId]);
    }

    /**
     * @param string $mixinId
     * @return Mixin
     * @throws MixinNotDefined
     */
    public function getMixin($mixinId)
    {
        if (isset($this->mixins[$mixinId])) {
            return $this->mixins[$mixinId];
        }

        if (isset($this->mixinsByCurie[$mixinId])) {
            return $this->mixinsByCurie[$mixinId];
        }

        throw new MixinNotDefined($this, $mixinId);
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

    /**
     * Returns an array of curies (string version).
     *
     * @return array
     */
    public function getMixinCuries()
    {
        return $this->mixinCuries;
    }
}
