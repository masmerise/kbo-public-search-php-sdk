<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type\Primitive;

use KboPublicSearch\Core\Utility\XmlText;
use Stringable;
use Webmozart\Assert\Assert;

/** The first name of a physical person, matched exactly. */
final readonly class FirstName implements Stringable
{
    private function __construct(private string $value)
    {
        Assert::lengthBetween($value, 1, 160, 'A first name must be between 1 and 160 characters.');
        XmlText::assert($value, 'A first name');
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
