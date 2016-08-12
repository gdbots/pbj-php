<?php

namespace Gdbots\Pbj\Serializer;

use Gdbots\Common\ToArray;
use Gdbots\Common\Util\ArrayUtils;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Enum\FieldRule;
use Gdbots\Pbj\Enum\TypeName;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Exception\EncodeValueFailed;
use Gdbots\Pbj\Exception\GdbotsPbjException;
use Gdbots\Pbj\Exception\InvalidResolvedSchema;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Message;
use Gdbots\Pbj\MessageRef;
use Gdbots\Pbj\MessageResolver;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\SchemaId;
use Gdbots\Pbj\WellKnown\DynamicField;
use Gdbots\Pbj\WellKnown\GeoPoint;

class PhpArraySerializer implements Serializer, Codec
{
    /**
     * Options for the serializer to use, e.g. json encoding options,
     * 'includeAllFields' which includes fields even if they're not set, etc.
     *
     * @var array
     */
    protected $options;

    /**
     * {@inheritdoc}
     */
    public function serialize(Message $message, array $options = [])
    {
        $this->options = $options;
        return $this->doSerialize($message);
    }

    /**
     * {@inheritdoc}
     * @return Message
     */
    public function deserialize($data, array $options = [])
    {
        $this->options = $options;

        Assertion::keyIsset(
            $data,
            Schema::PBJ_FIELD_NAME,
            sprintf(
                '[%s::%s] Array provided must contain the [%s] key.',
                get_called_class(),
                __FUNCTION__,
                Schema::PBJ_FIELD_NAME
            )
        );

        return $this->doDeserialize($data);
    }

    /**
     * @param Message $message
     * @param Field $field
     *
     * @return mixed
     */
    public function encodeMessage(Message $message, Field $field)
    {
        return $this->doSerialize($message);
    }

    /**
     * @param mixed $value
     * @param Field $field
     *
     * @return Message
     */
    public function decodeMessage($value, Field $field)
    {
        return $this->doDeserialize($value);
    }

    /**
     * @param MessageRef $messageRef
     * @param Field $field
     *
     * @return mixed
     */
    public function encodeMessageRef(MessageRef $messageRef, Field $field)
    {
        return $messageRef->toArray();
    }

    /**
     * @param mixed $value
     * @param Field $field
     *
     * @return MessageRef
     */
    public function decodeMessageRef($value, Field $field)
    {
        return MessageRef::fromArray($value);
    }

    /**
     * @param GeoPoint $geoPoint
     * @param Field $field
     *
     * @return mixed
     */
    public function encodeGeoPoint(GeoPoint $geoPoint, Field $field)
    {
        return $geoPoint->toArray();
    }

    /**
     * @param mixed $value
     * @param Field $field
     *
     * @return GeoPoint
     */
    public function decodeGeoPoint($value, Field $field)
    {
        return GeoPoint::fromArray($value);
    }

    /**
     * @param DynamicField $dynamicField
     * @param Field $field
     *
     * @return mixed
     */
    public function encodeDynamicField(DynamicField $dynamicField, Field $field)
    {
    }

    /**
     * @param mixed $value
     * @param Field $field
     *
     * @return DynamicField
     */
    public function decodeDynamicField($value, Field $field)
    {
    }

    /**
     * @param Message $message
     *
     * @return array
     */
    private function doSerialize(Message $message)
    {
        $schema = $message::schema();
        $message->validate();

        $payload = [];
        $includeAllFields = isset($this->options['includeAllFields']) && true === $this->options['includeAllFields'];

        foreach ($schema->getFields() as $field) {
            $fieldName = $field->getName();

            if (!$message->has($fieldName)) {
                if ($includeAllFields || $message->hasClearedField($fieldName)) {
                    $payload[$fieldName] = null;
                }

                continue;
            }

            $value = $message->get($fieldName);
            $type = $field->getType();

            switch ($field->getRule()->getValue()) {
                case FieldRule::A_SINGLE_VALUE:
                    $payload[$fieldName] = $type->encode($value, $field, $this);
                    break;

                case FieldRule::A_SET:
                case FieldRule::A_LIST:
                    $payload[$fieldName] = [];
                    foreach ($value as $v) {
                        $payload[$fieldName][] = $type->encode($v, $field, $this);
                    }
                    break;

                case FieldRule::A_MAP:
                    $payload[$fieldName] = [];
                    foreach ($value as $k => $v) {
                        $payload[$fieldName][$k] = $type->encode($v, $field, $this);
                    }
                    break;

                default:
                    break;
            }
        }

        return $payload;
    }

    /**
     * @param array $data
     * @return Message
     *
     * @throws \Exception
     * @throws GdbotsPbjException
     */
    private function doDeserialize(array $data)
    {
        $message = $this->createMessage((string) $data[Schema::PBJ_FIELD_NAME]);
        $schema = $message::schema();

        foreach ($data as $fieldName => $value) {
            if (!$schema->hasField($fieldName)) {
                continue;
            }

            if (null === $value) {
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
                    Assertion::isArray($value, sprintf('Field [%s] must be an array.', $fieldName), $fieldName);
                    $values = [];
                    foreach ($value as $v) {
                        $values[] = $type->decode($v, $field, $this);
                    }

                    if ($field->isASet()) {
                        $message->addToSet($fieldName, $values);
                    } else {
                        $message->addToList($fieldName, $values);
                    }
                    break;

                case FieldRule::A_MAP:
                    Assertion::true(
                        ArrayUtils::isAssoc($value),
                        sprintf('Field [%s] must be an associative array.', $fieldName),
                        $fieldName
                    );
                    foreach ($value as $k => $v) {
                        $message->addToMap($fieldName, $k, $type->decode($v, $field, $this));
                    }
                    break;

                default:
                    break;
            }
        }

        return $message->set(Schema::PBJ_FIELD_NAME, $schema->getId()->toString())->populateDefaults();
    }

    /**
     * @param mixed $value
     * @param Field $field
     *
     * @return mixed
     *
     * @throws EncodeValueFailed
     */
    private function encodeValue($value, Field $field)
    {
        $type = $field->getType();
        if ($type->encodesToScalar()) {
            return $type->encode($value, $field);
        }

        if ($value instanceof Message) {
            return $this->doSerialize($value);
        }

        if ($value instanceof ToArray) {
            return $value->toArray();
        }

        throw new EncodeValueFailed($value, $field, get_called_class() . ' has no handling for this value.');
    }

    /**
     * @param mixed $value
     * @param Field $field
     *
     * @return mixed
     *
     * @throws DecodeValueFailed
     */
    private function decodeValue($value, Field $field)
    {
        return $field->getType()->decode($value, $field, $this);
        /*
        if ($type->encodesToScalar()) {
            return $type->decode($value, $field);
        }

        if ($type->isMessage()) {
            return $this->deserialize($value);
        }

        if ($type->getTypeName() === TypeName::GEO_POINT()) {
            return GeoPoint::fromArray($value);
        }

        if ($type->getTypeName() === TypeName::MESSAGE_REF()) {
            return MessageRef::fromArray($value);
        }

        throw new DecodeValueFailed($value, $field, get_called_class() . ' has no handling for this value.');
        */
    }

    /**
     * @param string $schemaId
     * @return Message
     *
     * @throws GdbotsPbjException
     * @throws InvalidResolvedSchema
     */
    protected function createMessage($schemaId)
    {
        $schemaId = SchemaId::fromString($schemaId);
        $className = MessageResolver::resolveId($schemaId);

        /** @var Message $message */
        $message = new $className();
        Assertion::isInstanceOf($message, 'Gdbots\Pbj\Message');

        if ($message::schema()->getCurieMajor() !== $schemaId->getCurieMajor()) {
            throw new InvalidResolvedSchema($message::schema(), $schemaId, $className);
        }

        return $message;
    }
}
