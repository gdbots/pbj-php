<?php

namespace Gdbots\Pbj\Type;

use Gdbots\Common\Util\DateUtils;
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

        switch ($field->getFormat()->getValue()) {
            case Format::UNKNOWN:
                break;

            case Format::DATE:
                Assertion::regex($value, '/^\d{4}-\d{2}-\d{2}$/', $field->getName());
                break;

            case Format::DATE_TIME:
                Assertion::true(
                    DateUtils::isValidISO8601Date($value),
                    sprintf(
                        'Field [%s] must be a valid ISO8601 date-time.  Format must match [%s] or [%s].',
                        $field->getName(),
                        DateUtils::ISO8601,
                        \DateTime::ISO8601
                    ),
                    $field->getName()
                );
                break;

            case Format::EMAIL:
                Assertion::email($value, null, $field->getName());
                break;

            case Format::IPV4:
            case Format::IPV6:
                /*
                 * todo: need separate assertion for ipv4 and ipv6
                 */
                Assertion::url(
                    'http://' . $value,
                    sprintf(
                        'Field [%s] must be a valid [%s].',
                        $field->getName(),
                        $field->getFormat()->getValue()
                    ),
                    $field->getName()
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
                if (false === strpos($value, 'http')) {
                    $value = 'http://' . $value;
                }

                Assertion::url(
                    $value,
                    sprintf(
                        'Field [%s] must be a valid [%s].',
                        $field->getName(),
                        $field->getFormat()->getValue()
                    ),
                    $field->getName()
                );
                break;

            case Format::UUID:
                Assertion::uuid($value, null, $field->getName());
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