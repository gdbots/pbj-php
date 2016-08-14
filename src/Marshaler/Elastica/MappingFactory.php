<?php

namespace Gdbots\Pbj\Marshaler\Elastica;

use Elastica\Type\Mapping;
use Gdbots\Common\Util\SlugUtils;
use Gdbots\Common\Util\StringUtils;
use Gdbots\Pbj\Enum\Format;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Message;
use Gdbots\Pbj\Schema;

class MappingFactory
{
    /**
     * During the creation of a mapping any string types that are indexed will
     * use the "standard" analyzer unless something else is specified.
     * @link https://www.elastic.co/guide/en/elasticsearch/guide/current/custom-analyzers.html
     * @var string
     */
    protected $defaultAnalyzer = null;

    /**
     * Map of pbj type -> elastica mapping types.
     * @var array
     */
    protected $types = [
        'big-int'           => ['type' => 'long', 'include_in_all' => false],
        'binary'            => ['type' => 'binary'],
        'blob'              => ['type' => 'binary'],
        'boolean'           => ['type' => 'boolean', 'include_in_all' => false],
        'date'              => ['type' => 'date', 'include_in_all' => false],
        'date-time'         => ['type' => 'date', 'include_in_all' => false],
        'decimal'           => ['type' => 'double', 'include_in_all' => false],
        'dynamic-field'     => [
            'type' => 'object',
            'properties' => [
                'name'       => ['type' => 'string', 'index' => 'not_analyzed', 'include_in_all' => false],
                'bool_val'   => ['type' => 'boolean', 'include_in_all' => false],
                'date_val'   => ['type' => 'date', 'include_in_all' => false],
                'float_val'  => ['type' => 'float', 'include_in_all' => false],
                'int_val'    => ['type' => 'long', 'include_in_all' => false],
                'string_val' => ['type' => 'string'],
                'text_val'   => ['type' => 'string'],
            ]
        ],
        'float'             => ['type' => 'float', 'include_in_all' => false],
        'geo-point'         => ['type' => 'geo_point', 'include_in_all' => false],
        'identifier'        => ['type' => 'string', 'index' => 'not_analyzed', 'include_in_all' => false],
        'int'               => ['type' => 'long', 'include_in_all' => false],
        'int-enum'          => ['type' => 'integer', 'include_in_all' => false],
        'medium-blob'       => ['type' => 'binary'],
        'medium-int'        => ['type' => 'integer', 'include_in_all' => false],
        'medium-text'       => ['type' => 'string'],
        'message'           => ['type' => 'object'],
        'message-ref'       => [
            'type' => 'object',
            'properties' => [
                'curie' => ['type' => 'string', 'index' => 'not_analyzed', 'include_in_all' => false],
                'id'    => ['type' => 'string', 'index' => 'not_analyzed', 'include_in_all' => false],
                'tag'   => ['type' => 'string', 'index' => 'not_analyzed', 'include_in_all' => false],
            ]
        ],
        'microtime'         => ['type' => 'long', 'include_in_all' => false],
        'signed-big-int'    => ['type' => 'long', 'include_in_all' => false],
        'signed-int'        => ['type' => 'integer', 'include_in_all' => false],
        'signed-medium-int' => ['type' => 'long', 'include_in_all' => false],
        'signed-small-int'  => ['type' => 'short', 'include_in_all' => false],
        'signed-tiny-int'   => ['type' => 'byte', 'include_in_all' => false],
        'small-int'         => ['type' => 'integer', 'include_in_all' => false],
        'string'            => ['type' => 'string'],
        'string-enum'       => ['type' => 'string', 'index' => 'not_analyzed', 'include_in_all' => false],
        'text'              => ['type' => 'string'],
        'time-uuid'         => ['type' => 'string', 'index' => 'not_analyzed', 'include_in_all' => false],
        'timestamp'         => ['type' => 'date', 'include_in_all' => false],
        'tiny-int'          => ['type' => 'short', 'include_in_all' => false],
        'uuid'              => ['type' => 'string', 'index' => 'not_analyzed', 'include_in_all' => false],
    ];

    /**
     * Returns the custom analyzers that an index will need to when indexing some
     * pbj fields/types when certain options are used (urls, hashtag format, etc.)
     *
     * @link http://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-custom-analyzer.html
     *
     * @return array
     */
    public static function getCustomAnalyzers()
    {
        return [
            'pbj_keyword_analyzer' => [
                'tokenizer' => 'keyword',
                'filter' => 'lowercase',
            ]
        ];
    }

    /**
     * @param Schema $schema
     * @param string $defaultAnalyzer
     * @return Mapping
     */
    public function create(Schema $schema, $defaultAnalyzer = null)
    {
        $this->defaultAnalyzer = $defaultAnalyzer;
        $rootObject = new \stdClass();
        $rootObject->dynamic_templates = [];
        $mapping = new Mapping(null, $this->mapSchema($schema, $rootObject));
        foreach (get_object_vars($rootObject) as $k => $v) {
            if (!empty($v)) {
                $mapping->setParam($k, $v);
            }
        }
        return $mapping;
    }

    /**
     * @param Schema $schema
     * @param \stdClass $rootObject
     * @param string $path
     * @return array
     */
    protected function mapSchema(Schema $schema, \stdClass $rootObject, $path = null)
    {
        $map = [];

        foreach ($schema->getFields() as $field) {
            $fieldName = $field->getName();
            $type = $field->getType();
            $fieldPath = empty($path) ? $fieldName : $path . '.' . $fieldName;

            if ($fieldName === Schema::PBJ_FIELD_NAME) {
                $map[$fieldName] = ['type' => 'string', 'index' => 'not_analyzed', 'include_in_all' => false];
                continue;
            }

            $method = 'map' . ucfirst(StringUtils::toCamelFromSlug($type->getTypeValue()));

            if ($field->isAMap()) {
                $templateName = str_replace('-', '_', SlugUtils::create($fieldPath . '-template'));
                if (is_callable([$this, $method])) {
                    $rootObject->dynamic_templates[] = [
                        $templateName => [
                            'path_match' => $fieldPath . '.*',
                            'mapping' => $this->$method($field, $rootObject, $fieldPath),
                        ]
                    ];
                } else {
                    $rootObject->dynamic_templates[] = [
                        $templateName => [
                            'path_match' => $fieldPath . '.*',
                            'mapping' => $this->applyAnalyzer(
                                $this->types[$type->getTypeValue()],
                                $field,
                                $rootObject,
                                $path
                            )
                        ]
                    ];
                }
            } else {
                if (is_callable([$this, $method])) {
                    $map[$fieldName] = $this->$method($field, $rootObject, $fieldPath);
                } else {
                    $map[$fieldName] = $this->applyAnalyzer(
                        $this->types[$type->getTypeValue()],
                        $field,
                        $rootObject,
                        $path
                    );
                }
            }
        }

        return $map;
    }

    /**
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/nested.html
     *
     * @param Field $field
     * @param \stdClass $rootObject
     * @param string $path
     * @return array
     */
    protected function mapMessage(Field $field, \stdClass $rootObject, $path = null)
    {
        /** @var Message $class */
        $class = $field->getClassName();

        if (!empty($class) && !class_exists($class)) {
            /*
             * gdbots/pbjc compiler generates an interface and a concrete class with
             * a V# suffix.  v1 would of course generally exist so we have a good chance
             * of finding a class and thus a schema using this strategy.  we will however
             * need to get fancier as versions increase and when mixins are used.
             */
            $class = $class . 'V1';
        }

        if (!empty($class) && class_exists($class)) {
            $schema = $class::schema();
            return [
                'type' => $field->isAList() ? 'nested' : 'object',
                'properties' => $this->mapSchema($schema, $rootObject, $path)
            ];
        }

        return [
            'type' => $field->isAList() ? 'nested' : 'object',
            'properties' => [
                Schema::PBJ_FIELD_NAME => [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                    'include_in_all' => false
                ]
            ]
        ];
    }

    /**
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/nested.html
     *
     * @param Field $field
     * @param \stdClass $rootObject
     * @param string $path
     * @return array
     */
    protected function mapDynamicField(Field $field, \stdClass $rootObject, $path = null)
    {
        $mapping = $this->types[$field->getType()->getTypeValue()];

        if ($field->isAList()) {
            $mapping['type'] = 'nested';
        }

        $mapping['properties']['string_val'] = $this->applyAnalyzer(
            $mapping['properties']['string_val'], $field, $rootObject, $path
        );

        $mapping['properties']['text_val'] = $this->applyAnalyzer(
            $mapping['properties']['text_val'], $field, $rootObject, $path
        );

        return $mapping;
    }

    /**
     * @param Field $field
     * @param \stdClass $rootObject
     * @param string $path
     * @return array
     */
    protected function mapString(Field $field, \stdClass $rootObject, $path = null)
    {
        switch ($field->getFormat()->getValue()) {
            case Format::DATE:
            case Format::DATE_TIME:
                return $this->types['date-time'];

            /**
             * String fields with these formats should use "pbj_keyword_analyzer" (or something similar)
             * so searches on these fields are not case sensitive.
             *
             * @link http://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-custom-analyzer.html
             * @link http://stackoverflow.com/questions/15079064/how-to-setup-a-tokenizer-in-elasticsearch
             */
            case Format::SLUG:
            case Format::EMAIL:
            case Format::HOSTNAME:
            case Format::IPV6:
            case Format::UUID:
            case Format::URI:
            case Format::HASHTAG:
                return ['type' => 'string', 'analyzer' => 'pbj_keyword_analyzer', 'include_in_all' => false];

            case Format::IPV4:
                return ['type' => 'ip', 'include_in_all' => false];

            case Format::URL:
                return ['type' => 'string', 'index' => 'no', 'include_in_all' => false];

            default:
                if ($field->getPattern()) {
                    return ['type' => 'string', 'analyzer' => 'pbj_keyword_analyzer', 'include_in_all' => false];
                }

                return $this->applyAnalyzer(['type' => 'string'], $field, $rootObject, $path);
        }
    }

    /**
     * Modify the analyzer for a property prior to adding it to the document mapping.
     * This is only applied to "string" types.
     *
     * @param array $mapping
     * @param Field $field
     * @param \stdClass $rootObject
     * @param null $path
     * @return array
     */
    protected function applyAnalyzer(array $mapping, Field $field, \stdClass $rootObject, $path = null)
    {
        if (null === $this->defaultAnalyzer) {
            return $mapping;
        }

        if (!isset($mapping['type']) || 'string' != $mapping['type']) {
            return $mapping;
        }

        if (isset($mapping['index']) && 'analyzed' != $mapping['index']) {
            return $mapping;
        }

        if (isset($mapping['analyzer'])) {
            return $mapping;
        }

        $mapping['analyzer'] = $this->defaultAnalyzer;
        return $mapping;
    }
}
