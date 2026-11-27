<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Primitive;

use Stringable;
use Webmozart\Assert\Assert;

/** A municipality code (NIS code), as used in location filters. */
final readonly class MunicipalityCode implements Stringable
{
    private function __construct(private string $value)
    {
        Assert::regex($value, '/^\d{5}$/', 'A municipality code (NIS code) must consist of 5 digits.');
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
