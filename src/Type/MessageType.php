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

        $classNames = $field->getAnyOfClassNames();
        if (empty($classNames)) {
            // means it can be "any message"
            return;
        }

        foreach ($classNames as $className) {
            if ($value instanceof $className) {
                return;
            }
        }

        Assertion::true(
            false,
            sprintf(
                'Field [%s] must be an instance of at least one of: %s.',
                $field->getName(),
                implode(',', $classNames)
            ),
            $field->getName()
        );
    }

    public function encode($value, Field $field, ?Codec $codec = null)
    {
        return $codec->encodeMessage($value, $field);
    }

    public function decode($value, Field $field, ?Codec $codec = null)
    {
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
