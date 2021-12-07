<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Exception\DecodeValueFailed;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\WellKnown\NodeRef;

final class NodeRefType extends AbstractType
{
    public function guard(mixed $value, Field $field): void
    {
        Assertion::isInstanceOf($value, NodeRef::class, null, $field->getName());
    }

    public function encode(mixed $value, Field $field, ?Codec $codec = null): ?string
    {
        if ($value instanceof NodeRef) {
            return $value->toString();
        }

        return !empty($value) ? (string)$value : null;
    }

    public function decode(mixed $value, Field $field, ?Codec $codec = null): NodeRef|string|null
    {
        if (null === $value || $value instanceof NodeRef) {
            return $value;
        }

        if ($codec && $codec->skipValidation() && !empty($value)) {
            return (string)$value;
        }

        try {
            return NodeRef::fromString((string)$value);
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
}
