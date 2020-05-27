<?php
declare(strict_types=1);

namespace Gdbots\Pbj;

use Gdbots\Pbj\Exception\FieldAlreadyDefined;
use Gdbots\Pbj\Exception\FieldNotDefined;
use Gdbots\Pbj\Exception\FieldOverrideNotCompatible;
use Gdbots\Pbj\Util\ClassUtil;

final class Schema implements \JsonSerializable
{
    const PBJ_FIELD_NAME = '_schema';

    private string $className;
    private string $classShortName;
    private SchemaId $id;

    /** @var Field[] */
    private array $fields = [];

    /** @var Field[] */
    private array $requiredFields = [];

    private array $mixins = [];
    private array $mixinKeys = [];

    /**
     * @param SchemaId|string $id
     * @param string          $className
     * @param Field[]         $fields
     * @param string[]        $mixins
     */
    public function __construct($id, string $className, array $fields = [], array $mixins = [])
    {
        $this->id = $id instanceof SchemaId ? $id : SchemaId::fromString($id);
        $this->className = $className;
        $this->classShortName = ClassUtil::getShortName($this->className);

        $this->addField(
            FieldBuilder::create(self::PBJ_FIELD_NAME, Type\StringType::create())
                ->required()
                ->pattern(SchemaId::VALID_PATTERN)
                ->withDefault($this->id->toString())
                ->build()
        );

        foreach ($fields as $field) {
            $this->addField($field);
        }

        $this->mixins = $mixins;
        $this->mixinKeys = array_flip($mixins);
    }

    public function toString(): string
    {
        return $this->id->toString();
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'curie'       => $this->getCurie(),
            'curie_major' => $this->getCurieMajor(),
            'qname'       => $this->getQName(),
            'class_name'  => $this->className,
            'mixins'      => $this->mixins,
            'fields'      => $this->fields,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function __toString()
    {
        return $this->id->toString();
    }

    /**
     * @param Field $field
     *
     * @throws FieldAlreadyDefined
     * @throws FieldOverrideNotCompatible
     */
    private function addField(Field $field): void
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

    public function getId(): SchemaId
    {
        return $this->id;
    }

    public function getCurie(): SchemaCurie
    {
        return $this->id->getCurie();
    }

    /**
     * @see SchemaId::getCurieMajor
     */
    public function getCurieMajor(): string
    {
        return $this->id->getCurieMajor();
    }

    public function getQName(): SchemaQName
    {
        return $this->id->getCurie()->getQName();
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getClassShortName(): string
    {
        return $this->classShortName;
    }

    /**
     * Convenience method to return the name of the method that should
     * exist to handle this message.
     *
     * For example, an ImportUserV1 message would be handled by:
     * SomeClass::importUserV1(Message $command)
     *
     * @param bool   $withMajor
     * @param string $prefix
     *
     * @return string
     */
    public function getHandlerMethodName(bool $withMajor = true, ?string $prefix = null): string
    {
        if (null !== $prefix) {
            if ($withMajor) {
                return "{$prefix}{$this->classShortName}";
            }

            $method = str_replace('V' . $this->id->getVersion()->getMajor(), '', $this->classShortName);
            return "{$prefix}{$method}";
        }

        if ($withMajor) {
            return lcfirst($this->classShortName);
        }

        return lcfirst(str_replace('V' . $this->id->getVersion()->getMajor(), '', $this->classShortName));
    }

    /**
     * Convenience method that creates a message instance with this schema.
     *
     * @param array $data
     *
     * @return Message
     */
    public function createMessage(array $data = []): Message
    {
        /** @var Message $className */
        $className = $this->className;
        if (empty($data)) {
            return $className::create();
        }

        return $className::fromArray($data);
    }

    public function hasField(string $fieldName): bool
    {
        return isset($this->fields[$fieldName]);
    }

    /**
     * @param string $fieldName
     *
     * @return Field
     *
     * @throws FieldNotDefined
     */
    public function getField(string $fieldName): Field
    {
        if (!isset($this->fields[$fieldName])) {
            throw new FieldNotDefined($this, $fieldName);
        }

        return $this->fields[$fieldName];
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return Field[]
     */
    public function getRequiredFields(): array
    {
        return $this->requiredFields;
    }

    /**
     * Returns true if the mixin is on this schema.  Id provided can be
     * qualified to major rev or just the curie.
     *
     * @param string $mixin
     *
     * @return bool
     *
     * @see SchemaId::getCurieMajor
     *
     */
    public function hasMixin(string $mixin): bool
    {
        return isset($this->mixinKeys[$mixin]);
    }

    /**
     * @return string[]
     */
    public function getMixins(): array
    {
        return $this->mixins;
    }

    /**
     * @param SchemaCurie|string $curie
     *
     * @return bool
     */
    public function usesCurie($curie): bool
    {
        $curie = (string)$curie;

        if ($this->hasMixin($curie)) {
            return true;
        }

        if ($this->getCurie()->toString() === $curie) {
            return true;
        }

        if ($this->getCurieMajor() === $curie) {
            return true;
        }

        return false;
    }
}
