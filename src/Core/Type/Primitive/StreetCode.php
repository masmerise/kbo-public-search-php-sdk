<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Primitive;

use Stringable;
use Webmozart\Assert\Assert;

/** A street code, e.g. "0086". Only unique within its municipality. */
final readonly class StreetCode implements Stringable
{
    private function __construct(private string $value)
    {
        Assert::regex($value, '/^\S+$/', 'A street code must not be blank.');
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function equals(StreetCode $other): bool
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
