<?php

namespace Gdbots\Tests\Pbj\Marshaler\Elastica;

use Elastica\Client;
use Elastica\Index;
use Elastica\Type;
use Gdbots\Pbj\Marshaler\Elastica\MappingFactory;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;
use Gdbots\Tests\Pbj\Fixtures\MapsMessage;
use PHPUnit\Framework\TestCase;

class MappingFactoryTest extends TestCase
{
    /** @var MappingFactory */
    protected $factory;

    /** @var string */
    protected $indexName;

    public function setup()
    {
        $this->factory = new MappingFactory();
        $this->indexName = getenv('ELASTICA_INDEX') ?: 'pbj_tests';
    }

    public function testCreate()
    {
        $type = new Type(new Index(new Client(), $this->indexName), 'pbj_test_type');
        $schema = EmailMessage::schema();
        $mapping = $this->factory->create($schema, 'english');
        $mapping->setType($type);

        $expected = <<<JSON
{
    "pbj_test_type": {
        "properties": {
            "_schema": {
                "type": "keyword",
                "include_in_all": false
            },
            "id": {
                "type": "keyword",
                "include_in_all": false
            },
            "from_name": {
                "type": "text",
                "analyzer": "english"
            },
            "from_email": {
                "type": "keyword",
                "normalizer": "pbj_keyword",
                "include_in_all": false
            },
            "subject": {
                "type": "text",
                "analyzer": "english"
            },
            "body": {
                "type": "text",
                "analyzer": "english"
            },
            "priority": {
                "type": "integer",
                "include_in_all": false
            },
            "sent": {
                "type": "boolean",
                "include_in_all": false
            },
            "date_sent": {
                "type": "date",
                "include_in_all": false
            },
            "microtime_sent": {
                "type": "long",
                "include_in_all": false
            },
            "provider": {
                "type": "keyword",
                "include_in_all": false
            },
            "labels": {
                "type": "keyword",
                "normalizer": "pbj_keyword",
                "include_in_all": false
            },
            "unsubscribe_url": {
                "type": "keyword",
                "normalizer": "pbj_keyword",
                "include_in_all": false
            },
            "nested": {
                "type": "object",
                "properties": {
                    "_schema": {
                        "type": "keyword",
                        "include_in_all": false
                    },
                    "test1": {
                        "type": "text",
                        "analyzer": "english"
                    },
                    "test2": {
                        "type": "long",
                        "include_in_all": false
                    },
                    "location": {
                        "type": "geo_point",
                        "include_in_all": false
                    },
                    "refs": {
                        "type": "object",
                        "properties": {
                            "curie": {
                                "type": "keyword",
                                "include_in_all": false
                            },
                            "id": {
                                "type": "keyword",
                                "include_in_all": false
                            },
                            "tag": {
                                "type": "keyword",
                                "include_in_all": false
                            }
                        }
                    }
                }
            },
            "enum_in_set": {
                "type": "keyword",
                "include_in_all": false
            },
            "enum_in_list": {
                "type": "keyword",
                "include_in_all": false
            },
            "any_of_message": {
                "type": "nested",
                "properties": {
                    "_schema": {
                        "type": "keyword",
                        "include_in_all": false
                    }
                }
            },
            "dynamic_fields": {
                "type": "nested",
                "properties": {
                    "name": {
                        "type": "keyword",
                        "normalizer": "pbj_keyword",
                        "include_in_all": false
                    },
                    "bool_val": {
                        "type": "boolean",
                        "include_in_all": false
                    },
                    "date_val": {
                        "type": "date",
                        "include_in_all": false
                    },
                    "float_val": {
                        "type": "float",
                        "include_in_all": false
                    },
                    "int_val": {
                        "type": "long",
                        "include_in_all": false
                    },
                    "string_val": {
                        "type": "text",
                        "fields": {
                            "raw": {
                                "type": "keyword",
                                "normalizer": "pbj_keyword"
                            }
                        },
                        "analyzer": "english"
                    },
                    "text_val": {
                        "type": "text",
                        "analyzer": "english"
                    }
                }
            }
        }
    }
}
JSON;

        $this->assertSame($expected, json_encode($mapping->toArray(), JSON_PRETTY_PRINT));
        $schema = MapsMessage::schema();
        $mapping = $this->factory->create($schema, 'english');
        $mapping->setType($type);

        $expected = <<<JSON
{
    "pbj_test_type": {
        "properties": {
            "_schema": {
                "type": "keyword",
                "include_in_all": false
            }
        },
        "dynamic_templates": [
            {
                "bigint_template": {
                    "path_match": "BigInt.*",
                    "mapping": {
                        "type": "long",
                        "include_in_all": false
                    }
                }
            },
            {
                "binary_template": {
                    "path_match": "Binary.*",
                    "mapping": {
                        "type": "binary"
                    }
                }
            },
            {
                "blob_template": {
                    "path_match": "Blob.*",
                    "mapping": {
                        "type": "binary"
                    }
                }
            },
            {
                "boolean_template": {
                    "path_match": "Boolean.*",
                    "mapping": {
                        "type": "boolean",
                        "include_in_all": false
                    }
                }
            },
            {
                "datetime_template": {
                    "path_match": "DateTime.*",
                    "mapping": {
                        "type": "date",
                        "include_in_all": false
                    }
                }
            },
            {
                "date_template": {
                    "path_match": "Date.*",
                    "mapping": {
                        "type": "date",
                        "include_in_all": false
                    }
                }
            },
            {
                "decimal_template": {
                    "path_match": "Decimal.*",
                    "mapping": {
                        "type": "double",
                        "include_in_all": false
                    }
                }
            },
            {
                "dynamicfield_template": {
                    "path_match": "DynamicField.*",
                    "mapping": {
                        "type": "object",
                        "properties": {
                            "name": {
                                "type": "keyword",
                                "normalizer": "pbj_keyword",
                                "include_in_all": false
                            },
                            "bool_val": {
                                "type": "boolean",
                                "include_in_all": false
                            },
                            "date_val": {
                                "type": "date",
                                "include_in_all": false
                            },
                            "float_val": {
                                "type": "float",
                                "include_in_all": false
                            },
                            "int_val": {
                                "type": "long",
                                "include_in_all": false
                            },
                            "string_val": {
                                "type": "text",
                                "fields": {
                                    "raw": {
                                        "type": "keyword",
                                        "normalizer": "pbj_keyword"
                                    }
                                },
                                "analyzer": "english"
                            },
                            "text_val": {
                                "type": "text",
                                "analyzer": "english"
                            }
                        }
                    }
                }
            },
            {
                "float_template": {
                    "path_match": "Float.*",
                    "mapping": {
                        "type": "float",
                        "include_in_all": false
                    }
                }
            },
            {
                "geopoint_template": {
                    "path_match": "GeoPoint.*",
                    "mapping": {
                        "type": "geo_point",
                        "include_in_all": false
                    }
                }
            },
            {
                "identifier_template": {
                    "path_match": "Identifier.*",
                    "mapping": {
                        "type": "keyword",
                        "include_in_all": false
                    }
                }
            },
            {
                "intenum_template": {
                    "path_match": "IntEnum.*",
                    "mapping": {
                        "type": "integer",
                        "include_in_all": false
                    }
                }
            },
            {
                "int_template": {
                    "path_match": "Int.*",
                    "mapping": {
                        "type": "long",
                        "include_in_all": false
                    }
                }
            },
            {
                "mediumblob_template": {
                    "path_match": "MediumBlob.*",
                    "mapping": {
                        "type": "binary"
                    }
                }
            },
            {
                "mediumint_template": {
                    "path_match": "MediumInt.*",
                    "mapping": {
                        "type": "integer",
                        "include_in_all": false
                    }
                }
            },
            {
                "mediumtext_template": {
                    "path_match": "MediumText.*",
                    "mapping": {
                        "type": "text",
                        "analyzer": "english"
                    }
                }
            },
            {
                "messageref_template": {
                    "path_match": "MessageRef.*",
                    "mapping": {
                        "type": "object",
                        "properties": {
                            "curie": {
                                "type": "keyword",
                                "include_in_all": false
                            },
                            "id": {
                                "type": "keyword",
                                "include_in_all": false
                            },
                            "tag": {
                                "type": "keyword",
                                "include_in_all": false
                            }
                        }
                    }
                }
            },
            {
                "message_template": {
                    "path_match": "Message.*",
                    "mapping": {
                        "type": "object",
                        "properties": {
                            "_schema": {
                                "type": "keyword",
                                "include_in_all": false
                            },
                            "test1": {
                                "type": "text",
                                "analyzer": "english"
                            },
                            "test2": {
                                "type": "long",
                                "include_in_all": false
                            },
                            "location": {
                                "type": "geo_point",
                                "include_in_all": false
                            },
                            "refs": {
                                "type": "object",
                                "properties": {
                                    "curie": {
                                        "type": "keyword",
                                        "include_in_all": false
                                    },
                                    "id": {
                                        "type": "keyword",
                                        "include_in_all": false
                                    },
                                    "tag": {
                                        "type": "keyword",
                                        "include_in_all": false
                                    }
                                }
                            }
                        }
                    }
                }
            },
            {
                "microtime_template": {
                    "path_match": "Microtime.*",
                    "mapping": {
                        "type": "long",
                        "include_in_all": false
                    }
                }
            },
            {
                "signedbigint_template": {
                    "path_match": "SignedBigInt.*",
                    "mapping": {
                        "type": "long",
                        "include_in_all": false
                    }
                }
            },
            {
                "signedint_template": {
                    "path_match": "SignedInt.*",
                    "mapping": {
                        "type": "integer",
                        "include_in_all": false
                    }
                }
            },
            {
                "signedmediumint_template": {
                    "path_match": "SignedMediumInt.*",
                    "mapping": {
                        "type": "integer",
                        "include_in_all": false
                    }
                }
            },
            {
                "signedsmallint_template": {
                    "path_match": "SignedSmallInt.*",
                    "mapping": {
                        "type": "short",
                        "include_in_all": false
                    }
                }
            },
            {
                "signedtinyint_template": {
                    "path_match": "SignedTinyInt.*",
                    "mapping": {
                        "type": "byte",
                        "include_in_all": false
                    }
                }
            },
            {
                "smallint_template": {
                    "path_match": "SmallInt.*",
                    "mapping": {
                        "type": "integer",
                        "include_in_all": false
                    }
                }
            },
            {
                "stringenum_template": {
                    "path_match": "StringEnum.*",
                    "mapping": {
                        "type": "keyword",
                        "include_in_all": false
                    }
                }
            },
            {
                "string_template": {
                    "path_match": "String.*",
                    "mapping": {
                        "type": "text",
                        "analyzer": "english"
                    }
                }
            },
            {
                "text_template": {
                    "path_match": "Text.*",
                    "mapping": {
                        "type": "text",
                        "analyzer": "english"
                    }
                }
            },
            {
                "timestamp_template": {
                    "path_match": "Timestamp.*",
                    "mapping": {
                        "type": "date",
                        "include_in_all": false
                    }
                }
            },
            {
                "timeuuid_template": {
                    "path_match": "TimeUuid.*",
                    "mapping": {
                        "type": "keyword",
                        "include_in_all": false
                    }
                }
            },
            {
                "tinyint_template": {
                    "path_match": "TinyInt.*",
                    "mapping": {
                        "type": "short",
                        "include_in_all": false
                    }
                }
            },
            {
                "trinary_template": {
                    "path_match": "Trinary.*",
                    "mapping": {
                        "type": "byte",
                        "include_in_all": false
                    }
                }
            },
            {
                "uuid_template": {
                    "path_match": "Uuid.*",
                    "mapping": {
                        "type": "keyword",
                        "include_in_all": false
                    }
                }
            }
        ]
    }
}
JSON;

        $expected = json_decode($expected, true);
        $actual = $mapping->toArray();
        $expected['pbj_test_type']['dynamic_templates'] = $this->sortDynamicTemplates($expected['pbj_test_type']['dynamic_templates']);
        $actual['pbj_test_type']['dynamic_templates'] = $this->sortDynamicTemplates($actual['pbj_test_type']['dynamic_templates']);

        $this->assertSame(json_encode($expected['pbj_test_type']['properties']), json_encode($actual['pbj_test_type']['properties']));
        $this->assertSame(json_encode($expected['pbj_test_type']['dynamic_templates']), json_encode($actual['pbj_test_type']['dynamic_templates']));
        //echo json_encode($mapping->toArray(), JSON_PRETTY_PRINT);
    }

    /**
     * @param array $templates
     *
     * @return array
     */
    private function sortDynamicTemplates(array $templates)
    {
        $new = [];
        foreach ($templates as $k => $v) {
            $new[key($v)] = $v;
        }

        ksort($new);
        return $new;
    }
}
