<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Message;

final class MessageType extends AbstractType
{
    public function guard($value, Field $field): void
    {
        /** @var Message $value */
        Assertion::isInstanceOf($value, Message::class, null, $field->getName());

        if (!$field->hasAnyOfCuries()) {
            // means it can be "any message"
            return;
        }

        $curies = $field->getAnyOfCuries();
        $schema = $value::schema();
        foreach ($curies as $curie) {
            if ($schema->usesCurie($curie)) {
                return;
            }
        }

        Assertion::true(
            false,
            sprintf(
                'Field [%s] must be use at least one of: %s.',
                $field->getName(),
                implode(',', $curies)
            ),
            $field->getName()
        );
    }

    public function encode($value, Field $field, ?Codec $codec = null)
    {
        if (null === $value) {
            return null;
        }

        return $codec->encodeMessage($value, $field);
    }

    public function decode($value, Field $field, ?Codec $codec = null)
    {
        if (null === $value || $value instanceof Message) {
            return $value;
        }

        return $codec->decodeMessage($value, $field);
    }

    public function isScalar(): bool
    {
        return false;
    }

    public function encodesToScalar(): bool
    {
        return false;
    }

    public function isMessage(): bool
    {
        return true;
    }

    public function allowedInSet(): bool
    {
        return false;
    }
}
