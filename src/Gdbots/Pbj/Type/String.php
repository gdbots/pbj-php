<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Field;

final class String extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        Assertion::maxLength($value, 255, null, $field->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function encode($value, Field $field)
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function decode($value, Field $field)
    {
        // todo: do we auto truncate string or let the exception get thrown?
        // we cast ints and bools, so do we autofix this shit or not
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function isString()
    {
        return true;
    }

    /**
     * @param string $str
     * @param int $length
     * @return string
     */
    private function truncate($str, $length = 255)
    {
        $strLength = mb_strlen($str);
        if ($strLength <= $length) {
            return $str;
        }
        return mb_substr($str, 0, $length);
    }
}