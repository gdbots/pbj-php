<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Marshaler\Elastica;

use Elastica\Document;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Enum\FieldRule;
use Gdbots\Pbj\Exception\GdbotsPbjException;
use Gdbots\Pbj\Exception\InvalidResolvedSchema;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Message;
use Gdbots\Pbj\MessageResolver;
use Gdbots\Pbj\Schema;
use Gdbots\Pbj\SchemaId;
use Gdbots\Pbj\WellKnown\DynamicField;
use Gdbots\Pbj\WellKnown\GeoPoint;
use Gdbots\Pbj\WellKnown\MessageRef;

final class DocumentMarshaler implements Codec
{
    public function marshal(Message $message, ?Document $document = null): Document
    {
        $document = $document ?: new Document();
        return $document->setData($this->doMarshal($message));
    }

    /**
     * @param Document|array $documentOrSource
     *
     * @return Message
     *
     * @throws \Throwable
     * @throws GdbotsPbjException
     */
    public function unmarshal($documentOrSource): Message
    {
        if ($documentOrSource instanceof Document) {
            return $this->doUnmarshal($documentOrSource->getData());
        }

        return $this->doUnmarshal($documentOrSource);
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
        return [$geoPoint->getLongitude(), $geoPoint->getLatitude()];
    }

    public function decodeGeoPoint($value, Field $field): GeoPoint
    {
        return new GeoPoint($value[1], $value[0]);
    }

    public function encodeMessage(Message $message, Field $field)
    {
        return $this->doMarshal($message);
    }

    public function decodeMessage($value, Field $field): Message
    {
        return $this->doUnmarshal($value);
    }

    public function encodeMessageRef(MessageRef $messageRef, Field $field)
    {
        return $messageRef->toArray();
    }

    public function decodeMessageRef($value, Field $field): MessageRef
    {
        return MessageRef::fromArray($value);
    }

    private function doMarshal(Message $message): array
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

    private function doUnmarshal(array $data): Message
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
