<?php

namespace Gdbots\Messages\Type;

use Gdbots\Messages\FieldDescriptor;

interface Type
{
    /**
     * @return Type
     */
    public static function create();

    /**
     * @param FieldDescriptor $descriptor
     * @param mixed $value
     * @throws \Exception
     */
    public function guard(FieldDescriptor $descriptor, $value);

    /**
     * @param FieldDescriptor $descriptor
     * @param mixed $value
     * @return mixed
     */
    public function encode(FieldDescriptor $descriptor, $value);

    /**
     * @param FieldDescriptor $descriptor
     * @param mixed $value
     * @return mixed
     */
    public function decode(FieldDescriptor $descriptor, $value);
}