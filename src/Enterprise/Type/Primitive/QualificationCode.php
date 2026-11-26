<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type\Primitive;

use Stringable;
use Webmozart\Assert\Assert;

/** A qualification code (KBO code table "Quality"), e.g. "00084". */
final readonly class QualificationCode implements Stringable
{
    private function __construct(private string $value)
    {
        Assert::regex($value, '/^\d{5}$/', 'A qualification code must consist of 5 digits.');
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function equals(QualificationCode $other): bool
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
