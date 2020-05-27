<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Marshaler\Elastica;

use Elastica\Mapping;
use Gdbots\Pbj\Enum\Format;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Message;
use Gdbots\Pbj\MessageResolver;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\Util\ClassUtil;
use Gdbots\Pbj\Util\SlugUtil;
use Gdbots\Pbj\Util\StringUtil;

class MappingBuilder
{
    /**
     * Generally we use "__" to indicate a derived field but kibana won't recognize it.
     * So for now, we'll use "d__" to indicate a derived field for ES.
     *
     * @link  https://github.com/elastic/kibana/issues/2551
     * @link  https://github.com/elastic/kibana/issues/4762
     */
    const ALL_FIELD = 'd__all';
    const TYPE_FIELD = 'd__type';

    /**
     * Map of pbj type to elasticsearch data types.
     */
    const TYPES = [
        'big-int'           => ['type' => 'long'],
        'binary'            => ['type' => 'binary'],
        'blob'              => ['type' => 'binary'],
        'boolean'           => ['type' => 'boolean'],
        'date'              => ['type' => 'date'],
        'date-time'         => ['type' => 'date'],
        'decimal'           => ['type' => 'double'],
        'dynamic-field'     => [
            'type'       => 'object',
            'properties' => [
                'name'       => ['type' => 'keyword', 'normalizer' => 'pbj_keyword'],
                'bool_val'   => ['type' => 'boolean'],
                'date_val'   => ['type' => 'date'],
                'float_val'  => ['type' => 'float'],
                'int_val'    => ['type' => 'long'],
                'string_val' => [
                    'type'    => 'text',
                    'copy_to' => self::ALL_FIELD,
                    'fields'  => ['raw' => ['type' => 'keyword', 'normalizer' => 'pbj_keyword']],
                ],
                'text_val'   => ['type' => 'text', 'copy_to' => self::ALL_FIELD],
            ],
        ],
        'float'             => ['type' => 'float'],
        'geo-point'         => ['type' => 'geo_point'],
        'identifier'        => ['type' => 'keyword'],
        'int'               => ['type' => 'long'],
        'int-enum'          => ['type' => 'integer'],
        'medium-blob'       => ['type' => 'binary'],
        'medium-int'        => ['type' => 'integer'],
        'medium-text'       => ['type' => 'text', 'copy_to' => self::ALL_FIELD],
        'message'           => ['type' => 'object'],
        'message-ref'       => [
            'type'       => 'object',
            'properties' => [
                'curie' => ['type' => 'keyword'],
                'id'    => ['type' => 'keyword'],
                'tag'   => ['type' => 'keyword'],
            ],
        ],
        'microtime'         => ['type' => 'long'],
        'node-ref'          => ['type' => 'keyword'],
        'signed-big-int'    => ['type' => 'long'],
        'signed-int'        => ['type' => 'integer'],
        'signed-medium-int' => ['type' => 'integer'],
        'signed-small-int'  => ['type' => 'short'],
        'signed-tiny-int'   => ['type' => 'byte'],
        'small-int'         => ['type' => 'integer'],
        'string'            => ['type' => 'text', 'copy_to' => self::ALL_FIELD],
        'string-enum'       => ['type' => 'keyword'],
        'text'              => ['type' => 'text', 'copy_to' => self::ALL_FIELD],
        'time-uuid'         => ['type' => 'keyword'],
        'timestamp'         => ['type' => 'date'],
        'tiny-int'          => ['type' => 'short'],
        'trinary'           => ['type' => 'byte'],
        'uuid'              => ['type' => 'keyword'],
    ];

    const MAX_PATH_DEPTH = 4;

    /**
     * During the creation of a mapping any string types that are indexed will
     * use the "english" analyzer unless something else is specified.
     * @link https://www.elastic.co/guide/en/elasticsearch/guide/current/custom-analyzers.html
     */
    private string $analyzer = 'english';

    /**
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping.html
     */
    private array $properties = [];

    /**
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/dynamic-templates.html
     */
    private array $dynamicTemplates = [];

    /**
     * When mappings are created with nested messages the path is tracked
     * so the dynamic templates are correctly associated with the path.
     *
     * @var array
     */
    private array $path = [];

    /**
     * Returns the custom analyzers that an index will need to when indexing some
     * pbj fields/types when certain options are used (urls, hashtag format, etc.)
     *
     * @link http://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-custom-analyzer.html
     */
    public static function getCustomAnalyzers(): array
    {
        return [
            'pbj_keyword' => [
                'tokenizer' => 'keyword',
                'filter'    => 'lowercase',
            ],
        ];
    }

    /**
     * Returns the custom normalizers that an index will need to when indexing some
     * pbj fields/types when certain options are used (urls, hashtag format, etc.)
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-normalizers.html
     */
    public static function getCustomNormalizers(): array
    {
        return [
            'pbj_keyword' => [
                'type'        => 'custom',
                'char_filter' => [],
                'filter'      => ['lowercase', 'asciifolding'],
            ],
        ];
    }

    public function build(): Mapping
    {
        $properties = $this->properties;
        $properties[self::TYPE_FIELD] = ['type' => 'keyword'];
        $properties[self::ALL_FIELD] = ['type' => 'text', 'analyzer' => $this->analyzer];
        $mapping = new Mapping($properties);
        $dynamicTemplates = $this->getDynamicTemplates();
        if (!empty($dynamicTemplates)) {
            $mapping->setParam('dynamic_templates', $dynamicTemplates);
        }

        return $mapping;
    }

    public function setAnalyzer(string $analyzer): self
    {
        $this->analyzer = $analyzer;
        return $this;
    }

    public function addDynamicTemplate(string $name, array $template): self
    {
        $this->dynamicTemplates[$name] = [$name => $template];
        return $this;
    }

    public function getDynamicTemplates(): array
    {
        return array_values($this->dynamicTemplates);
    }

    public function addSchema(Schema $schema): self
    {
        $this->properties = array_replace_recursive($this->properties, $this->buildSchema($schema));
        return $this;
    }

    protected function buildSchema(Schema $schema): array
    {
        $properties = [];

        if ($this->getPathDepth() > self::MAX_PATH_DEPTH) {
            return $properties;
        }

        foreach ($schema->getFields() as $field) {
            $fieldName = $field->getName();
            $type = $field->getType();
            $this->enterField($fieldName);
            $path = $this->getPath();

            if ($fieldName === Schema::PBJ_FIELD_NAME) {
                $properties[$fieldName] = $this->filterProperties($schema, $field, $path, ['type' => 'keyword']);
                $this->leaveField();
                continue;
            }

            $method = 'build' . ucfirst(StringUtil::toCamelFromSlug($type->getTypeValue()));

            if ($field->isAMap()) {
                $templateName = str_replace('-', '_', SlugUtil::create($path . '-template'));
                if (is_callable([$this, $method])) {
                    $this->addDynamicTemplate($templateName, [
                        'path_match' => $path . '.*',
                        'mapping'    => $this->filterProperties($schema, $field, $path, $this->$method($field)),
                    ]);
                } else {
                    $this->addDynamicTemplate($templateName, [
                        'path_match' => $path . '.*',
                        'mapping'    => $this->filterProperties(
                            $schema, $field, $path, $this->withAnalyzer(self::TYPES[$type->getTypeValue()], $field)
                        ),
                    ]);
                }
            } else {
                if (is_callable([$this, $method])) {
                    $properties[$fieldName] = $this->filterProperties($schema, $field, $path, $this->$method($field));
                } else {
                    $properties[$fieldName] = $this->filterProperties(
                        $schema, $field, $path, $this->withAnalyzer(self::TYPES[$type->getTypeValue()], $field)
                    );
                }
            }

            $this->leaveField();
        }

        return $properties;
    }

    /**
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/nested.html
     *
     * @param Field $field
     *
     * @return array
     */
    protected function buildMessage(Field $field): array
    {
        $properties = [Schema::PBJ_FIELD_NAME => ['type' => 'keyword']];

        foreach ($this->getSupportedMessages($field) as $message) {
            $properties = array_replace_recursive($properties, $this->buildSchema($message::schema()));
        }

        return [
            'type'       => $field->isAList() ? 'nested' : 'object',
            'properties' => $properties,
        ];
    }

    /**
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/nested.html
     *
     * @param Field $field
     *
     * @return array
     */
    protected function buildDynamicField(Field $field): array
    {
        $properties = self::TYPES[$field->getType()->getTypeValue()];

        if ($field->isAList()) {
            $properties['type'] = 'nested';
        }

        $properties['properties']['string_val'] = $this->withAnalyzer($properties['properties']['string_val'], $field);
        $properties['properties']['text_val'] = $this->withAnalyzer($properties['properties']['text_val'], $field);

        return $properties;
    }

    protected function buildString(Field $field): array
    {
        return $this->withFormat($field);
    }

    protected function buildText(Field $field): array
    {
        return $this->withFormat($field);
    }

    protected function withFormat(Field $field): array
    {
        $format = $field->hasFormat() ? $field->getFormat()->getValue() : null;

        switch ($format) {
            case Format::DATE:
            case Format::DATE_TIME:
                return self::TYPES['date-time'];

            /**
             * String fields with these formats should use "pbj_keyword" (or something similar)
             * so searches on these fields are not case sensitive.
             *
             * @link http://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-custom-analyzer.html
             * @link http://stackoverflow.com/questions/15079064/how-to-setup-a-tokenizer-in-elasticsearch
             */
            case Format::EMAIL:
            case Format::HASHTAG:
            case Format::HOSTNAME:
            case Format::SLUG:
            case Format::URI:
            case Format::URL:
            case Format::UUID:
                return ['type' => 'keyword', 'normalizer' => 'pbj_keyword'];

            case Format::IPV4:
            case Format::IPV6:
                return ['type' => 'ip'];

            default:
                if ($field->getPattern()) {
                    return ['type' => 'keyword', 'normalizer' => 'pbj_keyword'];
                }

                return $this->withAnalyzer(self::TYPES['text'], $field);
        }
    }

    /**
     * Modify the analyzer for a property prior to adding it to the document mapping.
     * This is only applied to "text" types.
     *
     * @param array $properties
     * @param Field $field
     *
     * @return array
     */
    protected function withAnalyzer(array $properties, Field $field): array
    {
        if (null === $this->analyzer) {
            return $properties;
        }

        if (!isset($properties['type']) || 'text' !== $properties['type']) {
            return $properties;
        }

        if (isset($properties['index']) && false === $properties['index']) {
            return $properties;
        }

        if (isset($properties['analyzer'])) {
            return $properties;
        }

        $properties['analyzer'] = $this->analyzer;
        return $properties;
    }

    /**
     * Override to customize the properties for a given field.
     *
     * @param Schema $schema
     * @param Field  $field
     * @param string $path
     * @param array  $properties
     *
     * @return array
     */
    protected function filterProperties(Schema $schema, Field $field, string $path, array $properties): array
    {
        return $properties;
    }

    protected function enterField(string $fieldName): void
    {
        $this->path[] = $fieldName;
    }

    protected function leaveField(): void
    {
        array_pop($this->path);
    }

    protected function getPath(): string
    {
        return implode('.', $this->path);
    }

    protected function getPathDepth(): int
    {
        return count($this->path);
    }

    /**
     * @param Field $field
     *
     * @return Message[]
     */
    protected function getSupportedMessages(Field $field): array
    {
        if (!$field->hasAnyOfCuries()) {
            return MessageResolver::all();
        }

        $supported = [];
        $anyOfCuries = $field->getAnyOfCuries();

        /** @var Message|string $message */
        foreach (MessageResolver::all() as $message) {
            $constants = ClassUtil::getConstants($message);
            $curies = [];
            if (isset($constants['SCHEMA_CURIE'])) {
                $curies[] = $constants['SCHEMA_CURIE'];
                $curies[] = $constants['SCHEMA_CURIE_MAJOR'];
                $curies = array_merge($curies, $constants['MIXINS']);
            } else {
                $schema = $message::schema();
                $curies[] = $schema->getCurie()->toString();
                $curies[] = $schema->getCurieMajor();
                $curies = array_merge($curies, $schema->getMixins());
            }

            if (array_intersect($anyOfCuries, $curies)) {
                $supported[] = $message;
            }
        }

        return $supported;
    }
}
