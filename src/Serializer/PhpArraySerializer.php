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

    private bool $skipValidation = false;

    public function skipValidation(?bool $skipValidation = null): bool
    {
        if (null !== $skipValidation) {
            $this->skipValidation = $skipValidation;
        }

        return $this->skipValidation;
    }

    public function serialize(Message $message, array $options = []): mixed
    {
        $this->options = $options;
        return $this->doSerialize($message);
    }

    public function deserialize(mixed $data, array $options = []): Message
    {
        $this->options = $options;

        Assertion::keyIsset(
            $data,
            Schema::PBJ_FIELD_NAME,
            'Array provided must contain the [_schema] key.',
        );

        return $this->doDeserialize($data);
    }

    public function encodeDynamicField(DynamicField|array $dynamicField, Field $field): array
    {
        if ($dynamicField instanceof DynamicField) {
            return $dynamicField->toArray();
        }

        return $dynamicField;
    }

    public function decodeDynamicField(mixed $value, Field $field): DynamicField|array
    {
        if ($value instanceof DynamicField) {
            return $value;
        }

        if ($this->skipValidation()) {
            return $value;
        }

        return DynamicField::fromArray($value);
    }

    public function encodeGeoPoint(GeoPoint|array $geoPoint, Field $field): array
    {
        if ($geoPoint instanceof GeoPoint) {
            return $geoPoint->toArray();
        }

        return $geoPoint;
    }

    public function decodeGeoPoint(mixed $value, Field $field): GeoPoint|array
    {
        if ($value instanceof GeoPoint) {
            return $value;
        }

        if ($this->skipValidation()) {
            return $value;
        }

        return GeoPoint::fromArray($value);
    }

    public function encodeMessage(Message $message, Field $field): array
    {
        return $this->doSerialize($message);
    }

    public function decodeMessage(mixed $value, Field $field): Message
    {
        if ($value instanceof Message) {
            return $value;
        }

        return $this->doDeserialize($value);
    }

    public function encodeMessageRef(MessageRef|array $messageRef, Field $field): array
    {
        if ($messageRef instanceof MessageRef) {
            return $messageRef->toArray();
        }

        return $messageRef;
    }

    public function decodeMessageRef(mixed $value, Field $field): MessageRef|array
    {
        if ($value instanceof MessageRef) {
            return $value;
        }

        if ($this->skipValidation()) {
            return $value;
        }

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

            $value = $message->fget($fieldName);
            $type = $field->getType();

            if ($field->isASingleValue()) {
                $payload[$fieldName] = $type->encode($value, $field, $this);
            } else {
                $payload[$fieldName] = array_map(function ($v) use ($type, $field) {
                    return $type->encode($v, $field, $this);
                }, $value);
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

            if ($this->skipValidation()) {
                if ($field->isASingleValue()) {
                    $message->setWithoutValidation($fieldName, $type->decode($value, $field, $this));
                } else {
                    $message->setWithoutValidation($fieldName, array_map(function ($v) use ($type, $field) {
                        return $type->decode($v, $field, $this);
                    }, $value));
                }
                continue;
            }

            switch ($field->getRule()) {
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
                    Assertion::isArray($value, 'Field must be an associative array.', $fieldName);
                    foreach ($value as $k => $v) {
                        $message->addToMap($fieldName, $k, $type->decode($v, $field, $this));
                    }
                    break;

                default:
                    break;
            }
        }

        return $message->setWithoutValidation(Schema::PBJ_FIELD_NAME, $schema->getId()->toString())->populateDefaults();
    }
}
