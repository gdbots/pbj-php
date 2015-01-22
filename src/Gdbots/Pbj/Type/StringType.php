<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Enum\Format;
use Gdbots\Pbj\Field;

final class StringType extends AbstractStringType
{
    /**
     * {@inheritdoc}
     */
    public function guard($value, Field $field)
    {
        parent::guard($value, $field);

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
    public function getMaxBytes()
    {
        return 255;
    }
}