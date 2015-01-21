<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Common\Util\NumberUtils;
use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Enum\Format;
use Gdbots\Pbj\Field;

final class String extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        $maxLength = $field->getMaxLength() ?: 255;
        $maxLength = NumberUtils::bound($maxLength, 0, 255);
        $minLength = NumberUtils::bound($field->getMinLength(), 0, $maxLength);
        Assertion::betweenLength($value, $minLength, $maxLength, null, $field->getName());

        if ($pattern = $field->getPattern()) {
            Assertion::regex($value, $pattern, null, $field->getName());
        }

        // todo: add all semantic format handlers
        switch ($field->getFormat()->getValue()) {
            case Format::UNKNOWN:
                break;

            case Format::EMAIL:
                Assertion::email($value, null, $field->getName());
                break;

            default:
                break;
        }

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