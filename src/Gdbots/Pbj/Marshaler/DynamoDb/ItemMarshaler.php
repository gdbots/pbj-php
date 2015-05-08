<?php

namespace Gdbots\Pbj\Marshaler\DynamoDb;

use Aws\DynamoDb\Enum\Type;
use Gdbots\Common\GeoPoint;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Exception\InvalidResolvedSchema;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Enum\FieldRule;
use Gdbots\Pbj\Enum\TypeName;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Exception\EncodeValueFailed;
use Gdbots\Pbj\Exception\GdbotsPbjException;
use Gdbots\Pbj\Message;
use Gdbots\Pbj\MessageCurie;
use Gdbots\Pbj\MessageRef;
use Gdbots\Pbj\MessageResolver;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\SchemaId;

/**
 * Creates an array in the DynamoDb expected attribute value format.
 * @link http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_PutItem.html
 * @link http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_AttributeValue.html
 * @link http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/DataModel.html#DataModel.DataTypes
 *
 * @link http://blogs.aws.amazon.com/php/post/Tx3QE1CEXG8QG1Z/DynamoDB-JSON-and-Array-Marshaling-for-PHP
 */
final class ItemMarshaler
{
    /**
     * @param Message $message
     * @return array
     *
     * @throws \Exception
     * @throws GdbotsPbjException
     */
    public function marshal(Message $message)
    {
        $schema = $message::schema();
        $message->validate();

        $payload = [];

        foreach ($schema->getFields() as $field) {
            $fieldName = $field->getName();

            if (!$message->has($fieldName)) {
                if ($message->hasClearedField($fieldName)) {
                    $payload[$fieldName] = array('NULL' => true);
                }
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

    /**
     * Pass the Item of a result.  $result['Item']
     * @param array $data
     * @return Message
     *
     * @throws \Exception
     * @throws GdbotsPbjException
     */
    public function unmarshal(array $data)
    {
        return $this->doUnmarshal(['M' => $data]);
    }

    /**
     * @param array $data
     * @return Message
     *
     * @throws \Exception
     * @throws GdbotsPbjException
     */
    private function doUnmarshal(array $data)
    {
        Assertion::keyIsset(
            $data['M'],
            Schema::PBJ_FIELD_NAME,
            sprintf(
                '[%s::%s] Array provided must contain the [%s] key.',
                get_called_class(),
                __FUNCTION__,
                Schema::PBJ_FIELD_NAME
            )
        );

        $message = $this->createMessage((string) $data['M'][Schema::PBJ_FIELD_NAME]['S']);
        $schema = $message::schema();

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
            switch ($field->getRule()->getValue()) {
                case FieldRule::A_SINGLE_VALUE:
                    $message->setSingleValue($fieldName, $this->decodeValue($value, $field));
                    break;

                case FieldRule::A_SET:
                case FieldRule::A_LIST:
                    $values = [];
                    if ('L' === $dynamoType) {
                        foreach ($value as $v) {
                            $values[] = $this->decodeValue(isset($v['M']) ? $v['M'] : current($v), $field);
                        }
                    } else {
                        foreach ($value as $v) {
                            $values[] = $this->decodeValue($v, $field);
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
                        $message->addToMap($fieldName, $k, $this->decodeValue(current($v), $field));
                    }
                    break;

                default:
                    break;
            }
        }

        return $message->setSingleValue(Schema::PBJ_FIELD_NAME, $schema->getId()->toString())->populateDefaults();
    }

    /**
     * @param mixed $value
     * @param Field $field
     * @return mixed
     *
     * @throws EncodeValueFailed
     */
    private function encodeValue($value, Field $field)
    {
        $type = $field->getType();
        if ($type->encodesToScalar()) {
            if ($type->isBinary()) {
                return [Type::BINARY => $type->encode($value, $field)];
            } elseif ($type->isString()) {
                return [Type::STRING => $type->encode($value, $field)];
            } elseif ($type->isNumeric()) {
                return [Type::NUMBER => (string) $type->encode($value, $field)];
            } elseif ($type->isBoolean()) {
                return ['BOOL' => $type->encode($value, $field)];
            }
            throw new EncodeValueFailed($value, $field, get_called_class() . ' has no handling for this value.');
        }

        if ($value instanceof Message) {
            return ['M' => $this->marshal($value)];
        }

        if ($value instanceof GeoPoint) {
            return $this->encodeGeoPoint($value);
        }

        if ($value instanceof MessageRef) {
            return $this->encodeMessageRef($value);
        }

        throw new EncodeValueFailed($value, $field, get_called_class() . ' has no handling for this value.');
    }

    /**
     * @param array $value
     * @param Field $field
     * @return mixed
     *
     * @throws EncodeValueFailed
     */
    private function encodeASetValue(array $value, Field $field)
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
                $list[] = $this->encodeMessageRef($v);
            }
            return ['L' => $list];
        }

        if ($type->isBinary()) {
            $dynamoType = Type::BINARY_SET;
        } elseif ($type->isString()) {
            $dynamoType = Type::STRING_SET;
        } elseif ($type->isNumeric()) {
            $dynamoType = Type::NUMBER_SET;
        } else {
            throw new EncodeValueFailed(
                $value,
                $field,
                sprintf('%s::%s has no handling for this value.', get_called_class(), __FUNCTION__)
            );
        }

        $result = [];
        foreach ($value as $v) {
            if ($type->encodesToScalar()) {
                $result[] = (string) $type->encode($v, $field);
            } else {
                $result[] = (string) $v;
            }
        }

        return [$dynamoType => $result];
    }

    /**
     * @param GeoPoint $value
     * @return array
     */
    private function encodeGeoPoint(GeoPoint $value)
    {
        return [
            'M' => [
                'type' => [Type::STRING => 'Point'],
                'coordinates' => [
                    'L' => [
                        [Type::NUMBER => (string) $value->getLongitude()],
                        [Type::NUMBER => (string) $value->getLatitude()]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param MessageRef $value
     * @return array
     */
    private function encodeMessageRef(MessageRef $value)
    {
        return [
            'M' => [
                'curie' => [Type::STRING => $value->getCurie()->toString()],
                'id'    => [Type::STRING => $value->getId()],
                'tag'   => $value->hasTag() ? [Type::STRING => $value->getTag()] : ['NULL' => true]
            ]
        ];
    }

    /**
     * @param mixed $value
     * @param Field $field
     * @return mixed
     *
     * @throws DecodeValueFailed
     */
    private function decodeValue($value, Field $field)
    {
        $type = $field->getType();
        if ($type->encodesToScalar()) {
            return $type->decode($value, $field);
        }

        if ($type->isMessage()) {
            return $this->unmarshal($value);
        }

        if ($type->getTypeName() === TypeName::GEO_POINT()) {
            return new GeoPoint($value['coordinates']['L'][1]['N'], $value['coordinates']['L'][0]['N']);
        }

        if ($type->getTypeName() === TypeName::MESSAGE_REF()) {
            return new MessageRef(
                MessageCurie::fromString($value['curie']['S']),
                $value['id']['S'],
                isset($value['tag']['NULL']) ? null : $value['tag']['S']
            );
        }

        throw new DecodeValueFailed($value, $field, get_called_class() . ' has no handling for this value.');
    }

    /**
     * @param string $schemaId
     * @return Message
     *
     * @throws GdbotsPbjException
     * @throws InvalidResolvedSchema
     */
    private function createMessage($schemaId)
    {
        $schemaId = SchemaId::fromString($schemaId);
        $className = MessageResolver::resolveSchemaId($schemaId);

        /** @var Message $message */
        $message = new $className();
        Assertion::isInstanceOf($message, 'Gdbots\Pbj\Message');

        if ($message::schema()->getCurieWithMajorRev() !== $schemaId->getCurieWithMajorRev()) {
            throw new InvalidResolvedSchema($message::schema(), $schemaId, $className);
        }

        return $message;
    }
}