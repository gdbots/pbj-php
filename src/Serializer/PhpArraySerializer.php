<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Serializer;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Enum\FieldRule;
use Gdbots\Pbj\Exception\InvalidResolvedSchema;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Message;
use Gdbots\Pbj\MessageResolver;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\SchemaId;
use Gdbots\Pbj\WellKnown\DynamicField;
use Gdbots\Pbj\WellKnown\GeoPoint;
use Gdbots\Pbj\WellKnown\MessageRef;

class PhpArraySerializer implements Serializer, Codec
{
    /** Options for the serializer to use, e.g. json encoding options */
    protected array $options;

    public function serialize(Message $message, array $options = [])
    {
        $this->options = $options;
        return $this->doSerialize($message);
    }

    public function deserialize($data, array $options = []): Message
    {
        $this->options = $options;

        Assertion::keyIsset(
            $data,
            Schema::PBJ_FIELD_NAME,
            sprintf(
                '[%s::%s] Array provided must contain the [%s] key.',
                static::class,
                __FUNCTION__,
                Schema::PBJ_FIELD_NAME
            )
        );

        return $this->doDeserialize($data);
    }

    public function encodeDynamicField(DynamicField $dynamicField, Field $field)
    {
        return $dynamicField->toArray();
    }

    public function decodeDynamicField($value, Field $field): DynamicField
    {
        return DynamicField::fromArray($value);
    }

    public function encodeGeoPoint(GeoPoint $geoPoint, Field $field)
    {
        return $geoPoint->toArray();
    }

    public function decodeGeoPoint($value, Field $field): GeoPoint
    {
        return GeoPoint::fromArray($value);
    }

    public function encodeMessage(Message $message, Field $field)
    {
        return $this->doSerialize($message);
    }

    public function decodeMessage($value, Field $field): Message
    {
        return $this->doDeserialize($value);
    }

    public function encodeMessageRef(MessageRef $messageRef, Field $field)
    {
        return $messageRef->toArray();
    }

    public function decodeMessageRef($value, Field $field): MessageRef
    {
        return MessageRef::fromArray($value);
    }

    private function doSerialize(Message $message): array
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

    private function doDeserialize(array $data): Message
    {
        $schemaId = SchemaId::fromString((string)$data[Schema::PBJ_FIELD_NAME]);
        $className = MessageResolver::resolveId($schemaId);
        $message = new $className();
        Assertion::isInstanceOf($message, Message::class);
        $schema = $message::schema();

        if ($schema->getCurieMajor() !== $schemaId->getCurieMajor()) {
            throw new InvalidResolvedSchema($schema, $schemaId, $className);
        }

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
                    Assertion::isArray($value, sprintf('Field [%s] must be an associative array.', $fieldName), $fieldName);
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
}
