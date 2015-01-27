<?php

namespace Gdbots\Pbj\Serializer;

use Gdbots\Pbj\Exception\DeserializeMessageFailed;
use Gdbots\Pbj\Message;
use Symfony\Component\Yaml\Yaml;

class YamlSerializer extends PhpArraySerializer
{
    /**
     * {@inheritdoc}
     */
    public function serialize(Message $message, array $options = [])
    {
        return Yaml::dump(parent::serialize($message, $options));
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize($data, array $options = [])
    {
        if (!is_array($data)) {
            try {
                $data = Yaml::parse($data);
            } catch (\Exception $e) {
                throw new DeserializeMessageFailed($e->getMessage(), 0, $e);
            }
        }
        return parent::deserialize($data, $options);
    }
}
