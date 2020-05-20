<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Marshaler\DynamoDb;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Enum\FieldRule;
use Gdbots\Pbj\Enum\TypeName;
use Gdbots\Pbj\Exception\EncodeValueFailed;
use Gdbots\Pbj\Exception\InvalidResolvedSchema;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Message;
use Gdbots\Pbj\MessageResolver;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\SchemaCurie;
use Gdbots\Pbj\SchemaId;
use Gdbots\Pbj\WellKnown\DynamicField;
use Gdbots\Pbj\WellKnown\GeoPoint;
use Gdbots\Pbj\WellKnown\MessageRef;

/**
 * Creates an array in the DynamoDb expected attribute value format.
 * @link http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_PutItem.html
 * @link http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_AttributeValue.html
 * @link http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/DataModel.html#DataModel.DataTypes
 *
 * @link http://blogs.aws.amazon.com/php/post/Tx3QE1CEXG8QG1Z/DynamoDB-JSON-and-Array-Marshaling-for-PHP
 */
final class ItemMarshaler implements Codec
{
    const TYPE_S = 'S';
    const TYPE_N = 'N';
    const TYPE_B = 'B';
    const TYPE_SS = 'SS';
    const TYPE_NS = 'NS';
    const TYPE_BS = 'BS';
    const TYPE_STRING = 'S';
    const TYPE_NUMBER = 'N';
    const TYPE_BINARY = 'B';
    const TYPE_STRING_SET = 'SS';
    const TYPE_NUMBER_SET = 'NS';
    const TYPE_BINARY_SET = 'BS';

    private bool $skipValidation = false;

    public function skipValidation(?bool $skipValidation = null): bool
    {
        if (null !== $skipValidation) {
            $this->skipValidation = $skipValidation;
        }

        return $this->skipValidation;
    }

    public function marshal(Message $message): array
    {
        $schema = $message::schema();
        $message->validate();
        $payload = [];

        foreach ($schema->getFields() as $field) {
            $fieldName = $field->getName();

            if (!$message->has($fieldName)) {
                continue;
            }

            $value = $message->get($fieldName);

            switch ($field->getRule()->getValue()) {
                case FieldRule::A_SINGLE_VALUE:
                    $payload[$fieldName] = $this->encodeValue($value, $field);
                    break;

                case FieldRule::A_SET:
                    $payload[$fieldName] = $this->encodeASetValue($value, $field);
                    break;

                case FieldRule::A_LIST:
                    $list = [];
                    foreach ($value as $v) {
                        $list[] = $this->encodeValue($v, $field);
                    }
                    $payload[$fieldName] = ['L' => $list];
                    break;

                case FieldRule::A_MAP:
                    $map = [];
                    foreach ($value as $k => $v) {
                        $map[$k] = $this->encodeValue($v, $field);
                    }
                    $payload[$fieldName] = ['M' => $map];
                    break;

                default:
                    break;
            }
        }

        return $payload;
    }

    public function unmarshal(array $data): Message
    {
        return $this->doUnmarshal(['M' => $data]);
    }

    public function encodeDynamicField(DynamicField $dynamicField, Field $field)
    {
        return [
            'M' => [
                'name'                   => [self::TYPE_STRING => $dynamicField->getName()],
                $dynamicField->getKind() => $this->encodeValue($dynamicField->getValue(), $dynamicField->getField()),
            ],
        ];
    }

    public function decodeDynamicField($value, Field $field)
    {
        $data = ['name' => $value['name']['S']];
        unset($value['name']);

        $kind = key($value);
        $data[$kind] = current($value[$kind]);

        return DynamicField::fromArray($data);
    }

    public function encodeGeoPoint($geoPoint, Field $field)
    {
        return [
            'M' => [
                'type'        => [self::TYPE_STRING => 'Point'],
                'coordinates' => [
                    'L' => [
                        [self::TYPE_NUMBER => (string)$geoPoint->getLongitude()],
                        [self::TYPE_NUMBER => (string)$geoPoint->getLatitude()],
                    ],
                ],
            ],
        ];
    }

    public function decodeGeoPoint($value, Field $field)
    {
        return new GeoPoint((float)$value['coordinates']['L'][1]['N'], (float)$value['coordinates']['L'][0]['N']);
    }

    public function encodeMessage(Message $message, Field $field)
    {
        return ['M' => $this->marshal($message)];
    }

    public function decodeMessage($value, Field $field): Message
    {
        return $this->unmarshal($value);
    }

    public function encodeMessageRef($messageRef, Field $field)
    {
        return [
            'M' => [
                'curie' => [self::TYPE_STRING => $messageRef->getCurie()->toString()],
                'id'    => [self::TYPE_STRING => $messageRef->getId()],
                'tag'   => $messageRef->hasTag() ? [self::TYPE_STRING => $messageRef->getTag()] : ['NULL' => true],
            ],
        ];
    }

    public function decodeMessageRef($value, Field $field)
    {
        return new MessageRef(
            SchemaCurie::fromString($value['curie']['S']),
            $value['id']['S'],
            isset($value['tag']['NULL']) ? null : $value['tag']['S']
        );
    }

    private function doUnmarshal(array $data): Message
    {
        Assertion::keyIsset(
            $data['M'],
            Schema::PBJ_FIELD_NAME,
            'Array provided must contain the [_schema] key.'
        );

        $schemaId = SchemaId::fromString((string)$data['M'][Schema::PBJ_FIELD_NAME]['S']);
        $className = MessageResolver::resolveId($schemaId);
        $message = new $className();
        Assertion::isInstanceOf($message, Message::class);
        $schema = $message::schema();

        if ($schema->getCurieMajor() !== $schemaId->getCurieMajor()) {
            throw new InvalidResolvedSchema($schema, $schemaId, $className);
        }

        foreach ($data['M'] as $fieldName => $dynamoValue) {
            if (!$schema->hasField($fieldName)) {
                continue;
            }

            $dynamoType = key($dynamoValue);
            $value = current($dynamoValue);

            if ('NULL' === $dynamoType) {
                $message->clear($fieldName);
                continue;
            }

            $field = $schema->getField($fieldName);
            $type = $field->getType();

            switch ($field->getRule()->getValue()) {
                case FieldRule::A_SINGLE_VALUE:
                    $message->set($fieldName, $type->decode($value, $field, $this));
                    break;

                case FieldRule::A_SET:
                case FieldRule::A_LIST:
                    $values = [];
                    if ('L' === $dynamoType) {
                        foreach ($value as $v) {
                            $values[] = $type->decode(isset($v['M']) ? $v['M'] : current($v), $field, $this);
                        }
                    } else {
                        foreach ($value as $v) {
                            $values[] = $type->decode($v, $field, $this);
                        }
                    }

                    if ($field->isASet()) {
                        $message->addToSet($fieldName, $values);
                    } else {
                        $message->addToList($fieldName, $values);
                    }
                    break;

                case FieldRule::A_MAP:
                    foreach ($value as $k => $v) {
                        $message->addToMap($fieldName, $k, $type->decode(current($v), $field, $this));
                    }
                    break;

                default:
                    break;
            }
        }

        return $message->setWithoutValidation(Schema::PBJ_FIELD_NAME, $schema->getId()->toString())->populateDefaults();
    }

    private function encodeValue($value, Field $field): array
    {
        $type = $field->getType();

        if ($type->encodesToScalar()) {
            if ($type->isString()) {
                $value = $type->encode($value, $field, $this);
                if (empty($value)) {
                    return ['NULL' => true];
                } else {
                    return [self::TYPE_STRING => $value];
                }
            } elseif ($type->isNumeric()) {
                return [self::TYPE_NUMBER => (string)$type->encode($value, $field, $this)];
            } elseif ($type->isBoolean()) {
                return ['BOOL' => $type->encode($value, $field, $this)];
            } elseif ($type->isBinary()) {
                $value = $type->encode($value, $field, $this);
                if (empty($value)) {
                    return ['NULL' => true];
                } else {
                    return [self::TYPE_BINARY => $value];
                }
            }

            throw new EncodeValueFailed($value, $field, static::class . ' has no handling for this value.');
        }

        return $type->encode($value, $field, $this);
    }

    private function encodeASetValue(array $value, Field $field): array
    {
        $type = $field->getType();

        /*
         * A MessageRefType is the only object/map value that can be
         * used in a set.  In this case of DynamoDb, we can store it as
         * a list of maps.
         */
        if ($type->getTypeName() === TypeName::MESSAGE_REF()) {
            $list = [];
            foreach ($value as $v) {
                $list[] = $type->encode($v, $field, $this);
            }
            return ['L' => $list];
        }

        if ($type->isString()) {
            $dynamoType = self::TYPE_STRING_SET;
        } elseif ($type->isNumeric()) {
            $dynamoType = self::TYPE_NUMBER_SET;
        } elseif ($type->isBinary()) {
            $dynamoType = self::TYPE_BINARY_SET;
        } else {
            throw new EncodeValueFailed(
                $value,
                $field,
                sprintf('%s::%s has no handling for this value.', static::class, __FUNCTION__)
            );
        }

        $result = [];
        foreach ($value as $v) {
            if ($type->encodesToScalar()) {
                $result[] = (string)$type->encode($v, $field, $this);
            } else {
                $result[] = (string)$v;
            }
        }

        return [$dynamoType => $result];
    }
}
