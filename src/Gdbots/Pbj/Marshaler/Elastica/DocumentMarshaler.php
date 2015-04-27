<?php

namespace Gdbots\Pbj\Marshaler\Elastica;

use Elastica\Document;
use Gdbots\Common\Util\ArrayUtils;
use Gdbots\Common\GeoPoint;
use Gdbots\Common\ToArray;
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

class DocumentMarshaler
{
    /**
     * @param Message $message
     * @param Document $document
     * @return Document
     */
    public function marshal(Message $message, Document $document = null)
    {
        $document = $document ?: new Document();
        return $document->setData($this->doMarshal($message));
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
                    $payload[$fieldName] = null;
                }
                continue;
            }

            $value = $message->get($fieldName);

            switch ($field->getRule()->getValue()) {
                case FieldRule::A_SINGLE_VALUE:
                    $payload[$fieldName] = $this->encodeValue($value, $field);
                    break;

                case FieldRule::A_SET:
                case FieldRule::A_LIST:
                    $payload[$fieldName] = [];
                    foreach ($value as $v) {
                        $payload[$fieldName][] = $this->encodeValue($v, $field);
                    }
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
     * @param Document $document
     * @return Message
     */
    public function unmarshal(Document $document)
    {
        return $this->doUnmarshal($document->getData());
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
            $data,
            Schema::PBJ_FIELD_NAME,
            sprintf(
                '[%s::%s] Array provided must contain the [%s] key.',
                get_called_class(),
                __FUNCTION__,
                Schema::PBJ_FIELD_NAME
            )
        );

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

            switch ($field->getRule()->getValue()) {
                case FieldRule::A_SINGLE_VALUE:
                    $message->setSingleValue($fieldName, $this->decodeValue($value, $field));
                    break;

                case FieldRule::A_SET:
                case FieldRule::A_LIST:
                    Assertion::isArray($value, sprintf('Field [%s] must be an array.', $fieldName), $fieldName);
                    $values = [];
                    foreach ($value as $v) {
                        $values[] = $this->decodeValue($v, $field);
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
                        $message->addToMap($fieldName, $k, $this->decodeValue($v, $field));
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
            return $type->encode($value, $field);
        }

        if ($value instanceof Message) {
            return $this->doMarshal($value);
        }

        /**
         * @link http://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-geo-point-type.html#_lat_lon_as_array_5
         */
        if ($value instanceof GeoPoint) {
            return [$value->getLongitude(), $value->getLatitude()];
        }

        if ($value instanceof ToArray) {
            return $value->toArray();
        }

        throw new EncodeValueFailed($value, $field, get_called_class() . ' has no handling for this value.');
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