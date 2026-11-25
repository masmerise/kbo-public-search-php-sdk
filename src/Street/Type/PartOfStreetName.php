<?php declare(strict_types=1);

namespace KboPublicSearch\Street\Type;

use KboPublicSearch\Core\Utility\XmlText;
use Stringable;
use Webmozart\Assert\Assert;

/** Text that the street names must contain. */
final readonly class PartOfStreetName implements Stringable
{
    private function __construct(private string $value)
    {
        Assert::lengthBetween($value, 2, 32, 'Part of a street name must be between 2 and 32 characters.');
        XmlText::assert($value, 'Part of a street name');
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
