<?php

namespace Gdbots\Pbj\Serializer;

use Gdbots\Pbj\Exception\DeserializeMessageFailed;
use Gdbots\Pbj\Message;
use Symfony\Component\Yaml\Yaml;

class YamlSerializer extends PhpArraySerializer
{
    /**
     * {@inheritdoc}
     *
     * Note that the greater the yaml_inline option the slower it is.
     * 3 provides really good human readability but if you need
     * speed use 0 or 1.
     */
    public function serialize(Message $message, array $options = [])
    {
        if (!isset($options['yaml_inline'])) {
            $options['yaml_inline'] = 3;
        }

        if (!isset($options['yaml_indent'])) {
            $options['yaml_indent'] = 2;
        }

        return Yaml::dump(
            parent::serialize($message, $options),
            (int) $options['yaml_inline'],
            (int) $options['yaml_indent']
        );
    }

    /**
     * {@inheritdoc}
     *
     * @return Message
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
