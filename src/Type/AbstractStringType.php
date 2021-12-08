<?php
declare(strict_types=1);

namespace Gdbots\Pbj\Type;

use Gdbots\Pbj\Assertion;
use Gdbots\Pbj\Codec;
use Gdbots\Pbj\Enum\Format;
use Gdbots\Pbj\Field;
use Gdbots\Pbj\Util\DateUtil;
use Gdbots\Pbj\Util\HashtagUtil;
use Gdbots\Pbj\Util\NumberUtil;

abstract class AbstractStringType extends AbstractType
{
    public function guard(mixed $value, Field $field): void
    {
        $fieldName = $field->getName();
        Assertion::string($value, null, $fieldName);

        // intentionally using strlen to get byte length, not mb_strlen
        $length = strlen($value);
        $minLength = $field->getMinLength();
        $maxLength = NumberUtil::bound($field->getMaxLength(), $minLength, $this->getMaxBytes());
        $okay = $length >= $minLength && $length <= $maxLength;

        if (!$okay) {
            Assertion::true(
                $okay,
                sprintf(
                    'Field [%s] must be between [%d] and [%d] bytes, [%d] bytes given.',
                    $fieldName,
                    $minLength,
                    $maxLength,
                    $length
                ),
                $fieldName
            );
        }

        if ($pattern = $field->getPattern()) {
            Assertion::regex($value, $pattern, null, $fieldName);
        }

        if (!$field->hasFormat()) {
            return;
        }

        switch ($field->getFormat()) {
            case Format::DATE:
                Assertion::regex($value, '/^\d{4}-\d{2}-\d{2}$/', null, $fieldName);
                break;

            case Format::DATE_TIME:
                Assertion::true(
                    DateUtil::isValidISO8601Date($value),
                    'Field must be a valid ISO8601 date-time.',
                    $fieldName
                );
                break;

            case Format::SLUG:
                Assertion::regex($value, '/^([\w\/-]|[\w-][\w\/-]*[\w-])$/', null, $fieldName);
                break;

            case Format::EMAIL:
                Assertion::email($value, null, $fieldName);
                break;

            case Format::HASHTAG:
                Assertion::true(
                    HashtagUtil::isValid($value),
                    'Field must be a valid hashtag.',
                    $fieldName
                );
                break;

            case Format::IPV4:
                Assertion::url('https://' . $value, 'Field must be a valid IPV4.', $fieldName);
                break;

            case Format::IPV6:
                Assertion::url(
                    'https://[' . $value . ']',
                    'Field must be a valid IPV6.',
                    $fieldName
                );
                break;

            case Format::HOSTNAME:
            case Format::URI:
            case Format::URL:
                /*
                 * fixme: need better handling for HOSTNAME, URI and URL... assertion library just has one "url" handling
                 * but we really need separate ones for each of these formats.  right now we're just prefixing
                 * the value with a http so it looks like a url.  this won't work for thinks like mailto:
                 * urn:, etc.
                 */
                if (!str_contains($value, 'http')) {
                    $value = 'https://' . $value;
                }

                Assertion::url($value, 'Field must be a valid URL.', $fieldName);
                break;

            case Format::UUID:
                Assertion::uuid($value, null, $fieldName);
                break;

            default:
                break;
        }
    }

    public function encode(mixed $value, Field $field, ?Codec $codec = null): ?string
    {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        return $value;
    }

    public function decode(mixed $value, Field $field, ?Codec $codec = null): ?string
    {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        return $value;
    }

    public function isString(): bool
    {
        return true;
    }
}
