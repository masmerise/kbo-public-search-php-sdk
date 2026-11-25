<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type\Primitive;

use Stringable;
use Webmozart\Assert\Assert;

/** The last name of a natural person, matched exactly. */
final readonly class LastName implements Stringable
{
    private function __construct(private string $value)
    {
        Assert::lengthBetween($value, 1, 160, 'A last name must be between 1 and 160 characters.');
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
