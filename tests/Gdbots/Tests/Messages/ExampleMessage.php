<?php

namespace Gdbots\Tests\Messages;

use Gdbots\Messages\AbstractMessage;
use Gdbots\Messages\Field;
use Gdbots\Messages\FieldBuilder;
use Gdbots\Messages\Type;

class ExampleMessage extends AbstractMessage
{
    const FIRST_NAME = 'first_name';
    const LAST_NAME  = 'last_name';
    const AN_INT     = 'an_int';
    const A_BIG_INT  = 'a_big_int';

    /**
     * @return Field[]
     */
    protected static function getFields()
    {
        return [
            FieldBuilder::create(self::FIRST_NAME, Type\StringType::create())
                ->required()
                ->withAssertion(function ($value, Field $field) {
                    \Assert\that($value)->regex('/^[a-zA-Z]/', $field->getName() . ' must start with a letter.');
                })
                ->build(),

            FieldBuilder::create(self::LAST_NAME, Type\StringType::create())->build(),
            FieldBuilder::create(self::AN_INT,    Type\IntType::create())->build(),
            FieldBuilder::create(self::A_BIG_INT, Type\BigIntType::create())->build(),
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

    public function getAnInt()
    {
        return $this->get(self::AN_INT);
    }

    public function setAnInt($int)
    {
        $this->set(self::AN_INT, $int);
        return $this;
    }

    public function getABigInt()
    {
        return $this->get(self::A_BIG_INT);
    }

    public function setABigInt($int)
    {
        $this->set(self::A_BIG_INT, $int);
        return $this;
    }
}