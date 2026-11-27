<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Primitive;

use Stringable;
use Webmozart\Assert\Assert;

/** An authorization code (KBO code table "Permission"), e.g. "00504". Not the same table as AuthorizationSearchCode. */
final readonly class AuthorizationCode implements Stringable
{
    private function __construct(private string $value)
    {
        Assert::regex($value, '/^\d{5}$/', 'An authorization code must consist of 5 digits.');
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function equals(AuthorizationCode $other): bool
    {
        return $this->value === $other->value;
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
