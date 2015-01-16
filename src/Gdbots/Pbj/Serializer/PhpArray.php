<?php

namespace Gdbots\Pbj\Serializer;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Enum\FieldRule;
use Gdbots\Pbj\Message;
use Gdbots\Pbj\Schema;

class PhpArray extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    public function serialize(Message $message, array $options = [])
    {
        $schema = $message::schema();
        $message
            ->setSingleValue($schema::FIELD_NAME, $schema->getKey())
            ->validate();

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
                    $payload[$fieldName] = array_map(function($value) use ($field) {
                            return $field->encodeValue($value);
                        }, $value);
                    break;

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
        Assertion::isArray($data, sprintf('[%s::%s] requires a php array', __CLASS__, __FUNCTION__));

        // todo: get _curie field and generate MessageCurie
        // todo: get _sv field and generate SchemaVersion
        // todo: create MessageResolver to get className for curie and curie for className.

    }
}