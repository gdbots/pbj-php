<?php

namespace Gdbots\Pbj\Serializer;

use Gdbots\Common\GeoPoint;
use Gdbots\Common\ToArray;
use Gdbots\Common\Util\ArrayUtils;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Enum\FieldRule;
use Gdbots\Pbj\Enum\TypeName;
use Gdbots\Pbj\Exception\EncodeValueFailed;
use Gdbots\Pbj\Exception\GdbotsPbjException;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Message;
use Gdbots\Pbj\Schema;

class PhpArraySerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    public function serialize(Message $message, array $options = [])
    {
        return $this->doSerialize($message, $options);
    }

    /**
     * @param Message $message
     * @param array $options
     * @return array
     */
    private function doSerialize(Message $message, array $options)
    {
        $schema = $message::schema();
        $message->validate();

        $payload = [];
        $includeAllFields = isset($options['includeAllFields']) && true === $options['includeAllFields'];

        foreach ($schema->getFields() as $field) {
            $fieldName = $field->getName();

            if (!$message->has($fieldName)) {
                if ($includeAllFields || $message->hasClearedField($fieldName)) {
                    $payload[$fieldName] = null;
                }
                continue;
            }

            $value = $message->get($fieldName);

            switch ($field->getRule()->getValue()) {
                case FieldRule::A_SINGLE_VALUE:
                    $payload[$fieldName] = $this->encodeValue($value, $field, $options);
                    break;

                case FieldRule::A_SET:
                case FieldRule::A_LIST:
                    $payload[$fieldName] = [];
                    foreach ($value as $v) {
                        $payload[$fieldName][] = $this->encodeValue($v, $field, $options);
                    }
                    break;

                case FieldRule::A_MAP:
                    $payload[$fieldName] = [];
                    foreach ($value as $k => $v) {
                        $payload[$fieldName][$k] = $this->encodeValue($v, $field, $options);
                    }
                    break;

                default:
                    break;
            }
        }

        return $payload;
    }

    /**
     * {@inheritdoc}
     * @return Message
     */
    public function deserialize($data, array $options = [])
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

        return $this->doDeserialize($data, $options);
    }

    /**
     * @param array $data
     * @param array $options
     * @return Message
     *
     * @throws \Exception
     * @throws GdbotsPbjException
     */
    private function doDeserialize(array $data, array $options)
    {
        $message = $this->createMessage((string) $data[Schema::PBJ_FIELD_NAME]);
        $schema = $message::schema();

        foreach ($data as $fieldName => $value) {
            if (!$schema->hasField($fieldName)) {
                // todo: review, what to do with unknown fields
                continue;
            }

            $field = $schema->getField($fieldName);

            switch ($field->getRule()->getValue()) {
                case FieldRule::A_SINGLE_VALUE:
                    $message->setSingleValue($fieldName, $this->decodeValue($value, $field, $options));
                    break;

                case FieldRule::A_SET:
                case FieldRule::A_LIST:
                    Assertion::isArray($value, sprintf('Field [%s] must be an array.', $fieldName), $fieldName);
                    $values = [];
                    foreach ($value as $v) {
                        $values[] = $this->decodeValue($v, $field, $options);
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
                        $message->addToMap($fieldName, $k, $this->decodeValue($v, $field, $options));
                    }
                    break;

                default:
                    break;
            }
        }

        // todo: review, should we set the schema id on deserialize or serialize?
        // the message may be frozen when going to serialize.
        // deserializing a message is "upgrading" it potentially.  or downgrading.
        return $message->setSingleValue(Schema::PBJ_FIELD_NAME, $schema->getId()->toString())->populateDefaults();
    }

    /**
     * @param mixed $value
     * @param Field $field
     * @param array $options
     * @return mixed
     *
     * @throws EncodeValueFailed
     */
    private function encodeValue($value, Field $field, array $options)
    {
        $type = $field->getType();
        if ($type->encodesToScalar()) {
            return $type->encode($value, $field);
        }

        if ($value instanceof Message) {
            return $this->doSerialize($value, $options);
        }

        if ($value instanceof ToArray) {
            return $value->toArray();
        }

        throw new EncodeValueFailed($value, $field, get_called_class() . ' has no handling for this value.');
    }

    /**
     * @param mixed $value
     * @param Field $field
     * @param array $options
     * @return mixed
     */
    private function decodeValue($value, Field $field, array $options)
    {
        $type = $field->getType();
        if ($type->encodesToScalar()) {
            return $type->decode($value, $field);
        }

        if ($type->getTypeName() === TypeName::GEO_POINT()) {
            return GeoPoint::fromArray($value);
        }

        // assuming for now that everything else is a nested message
        return $this->deserialize($value, $options);
    }
}
