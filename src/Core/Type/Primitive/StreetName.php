<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Primitive;

use KboPublicSearch\Core\Utility\XmlText;
use Stringable;
use Webmozart\Assert\Assert;

/** A street name, as used in address searches. */
final readonly class StreetName implements Stringable
{
    private function __construct(private string $value)
    {
        Assert::lengthBetween($value, 2, 32, 'A street name must be between 2 and 32 characters.');
        XmlText::assert($value, 'A street name');
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
