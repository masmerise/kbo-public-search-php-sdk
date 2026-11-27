<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Primitive;

use Stringable;
use Webmozart\Assert\Assert;

/** A Belgian zipcode, as used in search requests. */
final readonly class Zipcode implements Stringable
{
    private function __construct(private string $value)
    {
        Assert::regex($value, '/^\d{4}$/', 'A zipcode must consist of 4 digits.');
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
