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
class ItemMarshaler
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
        return $this->doMarshal($message);
    }

    /**
     * @param Message $message
     * @return array
     */
    private function doMarshal(Message $message)
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
                    $payload[$fieldName] = [];
                    foreach ($value as $k => $v) {
                        $payload[$fieldName][$k] = $this->encodeValue($v, $field);
                    }
                    break;

                default:
                    break;
            }
        }

        return $payload;
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
            return ['M' => $this->doMarshal($value)];
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
            return $this->doUnmarshal($value);
        }

        if ($type->getTypeName() === TypeName::GEO_POINT()) {
            return new GeoPoint($value[1], $value[0]);
        }

        if ($type->getTypeName() === TypeName::MESSAGE_REF()) {
            return MessageRef::fromArray($value);
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