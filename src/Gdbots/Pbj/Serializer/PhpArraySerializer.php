<?php

namespace Gdbots\Pbj\Serializer;

use Gdbots\Common\Util\ArrayUtils;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Enum\FieldRule;
use Gdbots\Pbj\Exception\GdbotsPbjException;
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
        $message->setSingleValue(Schema::FIELD_NAME, $schema->getId()->toString())->validate();

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
                    $payload[$fieldName] = $field->encodeValue($value);
                    break;

                case FieldRule::A_SET:
                case FieldRule::A_LIST:
                    $payload[$fieldName] = array_map(function($value) use ($field) {
                            return $field->encodeValue($value);
                        }, $value);
                    break;

                case FieldRule::A_MAP:
                    $payload[$fieldName] = [];
                    foreach ($value as $k => $v) {
                        $payload[$fieldName][$k] = $field->encodeValue($v);
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
     */
    public function deserialize($data, array $options = [])
    {
        Assertion::keyIsset(
            $data,
            Schema::FIELD_NAME,
            sprintf(
                '[%s::%s] Array provided must contain the [%s] key.',
                get_called_class(),
                __FUNCTION__,
                Schema::FIELD_NAME
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
        $message = $this->createMessage((string) $data[Schema::FIELD_NAME]);
        $schema = $message::schema();

        foreach ($data as $fieldName => $value) {
            if (!$schema->hasField($fieldName)) {
                // todo: review, what to do with unknown fields
                continue;
            }

            $field = $schema->getField($fieldName);

            switch ($field->getRule()->getValue()) {
                case FieldRule::A_SINGLE_VALUE:
                    $message->setSingleValue($fieldName, $field->decodeValue($value));
                    break;

                case FieldRule::A_SET:
                    Assertion::isArray($value, sprintf('Field [%s] must be an array.', $fieldName), $fieldName);
                    foreach ($value as $v) {
                        $message->addToSet($fieldName, [$field->decodeValue($v)]);
                    }
                    break;

                case FieldRule::A_LIST:
                    Assertion::isArray($value, sprintf('Field [%s] must be an array.', $fieldName), $fieldName);
                    foreach ($value as $v) {
                        $message->addToList($fieldName, [$field->decodeValue($v)]);
                    }
                    break;

                case FieldRule::A_MAP:
                    Assertion::true(
                        ArrayUtils::isAssoc($value),
                        sprintf('Field [%s] must be an associative array.', $fieldName),
                        $fieldName
                    );
                    foreach ($value as $k => $v) {
                        $message->addToMap($fieldName, $k, $field->decodeValue($v));
                    }
                    break;

                default:
                    break;
            }
        }

        return $message->populateDefaults();
    }
}
