<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Serializer;

use Gdbots\Pbj\Exception\DeserializeMessageFailed;
use Gdbots\Pbj\Message;

class JsonSerializer extends PhpArraySerializer
{
    public function serialize(Message $message, array $options = [])
    {
        if (isset($options['json_encode_options'])) {
            return json_encode(parent::serialize($message, $options), $options['json_encode_options']);
        }

        return json_encode(parent::serialize($message, $options));
    }

    public function deserialize($data, array $options = []): Message
    {
        if (!is_array($data)) {
            $data = json_decode($data, true);
            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new DeserializeMessageFailed($this->getLastErrorMessage());
            }
        }

        return parent::deserialize($data, $options);
    }

    private function getLastErrorMessage(): string
    {
        if (function_exists('json_last_error_msg')) {
            return json_last_error_msg();
        }

        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch';
            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON';
            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
            default:
                return 'Unknown error';
        }
    }
}
