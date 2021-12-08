<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Serializer;

use Gdbots\Pbj\Exception\DeserializeMessageFailed;
use Gdbots\Pbj\Message;

class JsonSerializer extends PhpArraySerializer
{
    public function serialize(Message $message, array $options = []): string
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
                throw new DeserializeMessageFailed(json_last_error_msg());
            }
        }

        return parent::deserialize($data, $options);
    }
}
