<?php

namespace Gdbots\Pbj\Extension;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Enum\TypeName;
use Gdbots\Pbj\Exception\LogicException;
use Gdbots\Pbj\FieldBuilder as Fb;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\Type as T;

class EntitySchema extends Schema
{
    const ENTITY_ID_FIELD_NAME = 'id';
    const ETAG_FIELD_NAME = 'etag';
    const CREATED_AT_FIELD_NAME = 'created_at';
    const UPDATED_AT_FIELD_NAME = 'updated_at';

    /**
     * {@inheritdoc}
     */
    protected function getExtendedSchemaFields()
    {
        /** @var Entity $className */
        $className = $this->getClassName();
        if (!method_exists($className, 'defineEntityIdField')) {
            throw new LogicException(sprintf('Class [%s] must be an Entity to use the EntitySchema.', $className));
        }

        $idField = $className::defineEntityIdField();
        Assertion::isInstanceOf($idField, 'Gdbots\Pbj\Field');
        $typeName = $idField->getType()->getTypeName();

        if ($idField->getName() !== self::ENTITY_ID_FIELD_NAME || !$idField->isRequired()) {
            throw new LogicException(
                sprintf(
                    'Field returned from [%s::defineEntityIdField] must be named [%s] and be required.',
                    $className,
                    self::ENTITY_ID_FIELD_NAME
                )
            );
        }

        if ($typeName !== TypeName::TIME_UUID() && $typeName !== TypeName::UUID()) {
            throw new LogicException(
                sprintf(
                    'Field returned from [%s::defineEntityIdField] must be a TimeUuid or Uuid type, [%s] given.',
                    $className,
                    $typeName
                )
            );
        }

        return [
            $idField,
            Fb::create(self::ETAG_FIELD_NAME, T\StringType::create())
                ->pattern('/^[A-Za-z0-9_\-]+$/')
                ->maxLength(100)
                ->build(),
            Fb::create(self::CREATED_AT_FIELD_NAME, T\MicrotimeType::create())
                ->required()
                ->build(),
            Fb::create(self::UPDATED_AT_FIELD_NAME, T\MicrotimeType::create())
                ->useTypeDefault(false)
                ->build(),
        ];
    }
}
