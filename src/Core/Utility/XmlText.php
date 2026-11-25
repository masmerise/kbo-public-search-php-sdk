<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Utility;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

/** @internal Text that is sent in an envelope must be valid XML 1.0 character data. */
final readonly class XmlText
{
    /** @throws InvalidArgumentException when the text is not valid UTF-8 or contains control characters */
    public static function assert(string $value, string $what): void
    {
        Assert::true(
            mb_check_encoding($value, 'UTF-8') && preg_match('/[\x00-\x1F\x7F]/', $value) !== 1,
            "{$what} must be valid UTF-8 without control characters.",
        );
    }
}
