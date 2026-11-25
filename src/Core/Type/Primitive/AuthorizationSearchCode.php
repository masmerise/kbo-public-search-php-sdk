<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Primitive;

use KboPublicSearch\Core\Utility\XmlText;
use Stringable;
use Webmozart\Assert\Assert;

/** An authorization search code (code list AUTHORIZATION_SEARCH_CODE). */
final readonly class AuthorizationSearchCode implements Stringable
{
    private function __construct(private string $value)
    {
        Assert::length($value, 4, 'An authorization search code must be exactly 4 characters, e.g. "T004".');
        XmlText::assert($value, 'An authorization search code');
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
