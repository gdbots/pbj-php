<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\WellKnown\Identifier;

final class IdentifierType extends AbstractType
{
    public function guard(mixed $value, Field $field): void
    {
        $fieldName = $field->getName();
        /** @var Identifier $value */
        // Assertion::isInstanceOf($value, Identifier::class, null, $fieldName);
        Assertion::isInstanceOf($value, $field->getClassName(), null, $fieldName);
        $v = $value->toString();
        //Assertion::string($v, null, $field->getName());

        // intentionally using strlen to get byte length, not mb_strlen
        $length = strlen($v);
        $maxBytes = $this->getMaxBytes();
        $okay = $length > 0 && $length <= $maxBytes;

        if (!$okay) {
            Assertion::true(
                $okay,
                sprintf(
                    'Field [%s] must be between [1] and [%d] bytes, [%d] bytes given.',
                    $fieldName,
                    $maxBytes,
                    $length
                ),
                $fieldName
            );
        }
    }

    public function encode(mixed $value, Field $field, ?Codec $codec = null): ?string
    {
        if ($value instanceof Identifier) {
            return (string)$value->toString();
        }

        return !empty($value) ? (string)$value : null;
    }

    public function decode(mixed $value, Field $field, ?Codec $codec = null): Identifier|string|null
    {
        if (null === $value || $value instanceof Identifier) {
            return $value;
        }

        if ($codec && $codec->skipValidation() && !empty($value)) {
            return (string)$value;
        }

        /** @var Identifier $className */
        $className = $field->getClassName();

        try {
            return $className::fromString((string)$value);
        } catch (\Throwable $e) {
            throw new DecodeValueFailed($value, $field, $e->getMessage());
        }
    }

    public function isScalar(): bool
    {
        return false;
    }

    public function isString(): bool
    {
        return true;
    }

    public function getMaxBytes(): int
    {
        return 255;
    }
}
