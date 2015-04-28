<?php

namespace Gdbots\Pbj\Marshaler\Elastica;

use Elastica\Type\Mapping;
use Gdbots\Common\Util\StringUtils;
use Gdbots\Pbj\Enum\FieldRule;
use Gdbots\Pbj\Enum\Format;
use Gdbots\Pbj\Exception\EncodeValueFailed;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Message;
use Gdbots\Pbj\Schema;

class MappingBuilder
{
    /**
     * Map of pbj type -> elastica mapping types.
     * @var array
     */
    protected $types = [
        'big-int' => 'long',
        'binary' => 'binary',
        'blob' => 'binary',
        'boolean' => 'boolean',
        'date' => 'dateOptionalTime',
        'date-time' => 'dateOptionalTime',
        'decimal' => 'double',
        'float' => 'float',
        'geo-point' => 'geo_point',
        'identifier' => 'string',
        'int' => 'long',
        'int-enum' => 'integer',
        'medium-blob' => 'binary',
        'medium-int' => 'integer',
        'medium-text' => 'string',
        'message' => 'nested',
        'message-ref' => 'object',
        'microtime' => 'long',
        'signed-big-int' => 'long',
        'signed-int' => 'integer',
        'signed-medium-int' => 'long',
        'signed-small-int' => 'short',
        'signed-tiny-int' => 'byte',
        'small-int' => 'integer',
        'string' => 'string',
        'string-enum' => 'string',
        'text' => 'string',
        'time-uuid' => 'string',
        'timestamp' => 'dateOptionalTime',
        'tiny-int' => 'short',
        'uuid' => 'string'
    ];

    /**
     * @param Schema $schema
     * @return Mapping
     */
    public function build(Schema $schema)
    {
        return new Mapping(null, $this->mapSchema($schema));
    }

    /**
     * @param Schema $schema
     * @param bool $root
     * @return array
     */
    protected function mapSchema(Schema $schema, $root = true)
    {
        $map = [];

        foreach ($schema->getFields() as $field) {
            $fieldName = $field->getName();
            $type = $field->getType();

            if ($fieldName === Schema::PBJ_FIELD_NAME) {
                $map[$fieldName] = [
                    'type' => $this->types[$type->getTypeValue()],
                    'index' => 'not_analyzed',
                ];
                continue;
            }

            $method = 'map' . ucfirst(StringUtils::toCamelFromSlug($type->getTypeValue()));

            if (is_callable([$this, $method])) {
                $map[$fieldName] = $this->$method($field, $root);
            } else {
                $map[$fieldName] = ['type' => $this->types[$type->getTypeValue()]];
            }
        }

        return $map;
    }

    /**
     * @param Field $field
     * @return array
     */
    protected function mapMessage(Field $field)
    {
        $type = $field->getType();

        /** @var Message $class */
        $class = $field->getClassName();
        if (class_exists($class)) {
            $schema = $class::schema();
            return [
                'type' => $this->types[$type->getTypeValue()],
                'properties' => $this->mapSchema($schema)
            ];
        }

        return ['type' => $this->types[$type->getTypeValue()]];
    }

    /**
     * todo: autoset most likely/useful analyzers?
     *
     * @param Field $field
     * @return array
     */
    protected function mapString(Field $field)
    {
        switch ($field->getFormat()->getValue()) {
            case Format::DATE:
            case Format::DATE_TIME:
                return ['type' => $this->types['date-time']];

            case Format::DATED_SLUG:
            case Format::SLUG:
            case Format::HASHTAG:
            case Format::UUID:
                return [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ];

            case Format::IPV4:
                return ['type' => 'ip'];

            default:
                return ['type' => 'string'];
        }
    }
}