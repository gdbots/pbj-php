<?php

namespace Gdbots\Messages\Codec;

use Gdbots\Messages\Enum\FieldRule;
use Gdbots\Messages\Message;

class PhpArray extends AbstractCodec
{
    /**
     * {@inheritdoc}
     */
    public function encode(Message $message, $includeAllFields = false)
    {
        $payload = [];

        foreach ($message::fields() as $field) {
            $fieldName = $field->getName();

            if (!$message->has($fieldName)) {
                if ($includeAllFields) {
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
    public function decode(Message $message, $data)
    {
    }
}