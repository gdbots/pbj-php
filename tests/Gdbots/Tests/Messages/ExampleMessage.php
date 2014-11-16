<?php

namespace Gdbots\Tests\Messages;

use Gdbots\Messages\AbstractMessage;
use Gdbots\Messages\FieldDescriptor;
use Gdbots\Messages\Type\StringType;

class ExampleMessage extends AbstractMessage
{
    const FIRST_NAME = 'first_name';
    const LAST_NAME  = 'last_name';

    /**
     * @return FieldDescriptor[]
     */
    protected static function getFieldDescriptors()
    {
        return [
            new FieldDescriptor(self::FIRST_NAME, StringType::create()),
            new FieldDescriptor(self::LAST_NAME,  StringType::create()),
        ];
    }

    public function getFirstName()
    {
        return $this->get(self::FIRST_NAME);
    }

    public function setFirstName($str)
    {
        $this->set(self::FIRST_NAME, $str);
        return $this;
    }

    public function getLastName()
    {
        return $this->get(self::LAST_NAME);
    }

    public function setLastName($str)
    {
        $this->set(self::LAST_NAME, $str);
        return $this;
    }
}