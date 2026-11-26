<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Primitive;

use Stringable;
use Webmozart\Assert\Assert;

/** A NACEBEL activity code as KBO registers it, e.g. "62010"; its meaning depends on the NacebelVersion next to it. */
final readonly class NacebelCode implements Stringable
{
    private function __construct(private string $value)
    {
        Assert::regex($value, '/^\d{2,7}$/', 'A NACEBEL code must consist of 2 to 7 digits.');
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function equals(NacebelCode $other): bool
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
