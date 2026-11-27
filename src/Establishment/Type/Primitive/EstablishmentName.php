<?php declare(strict_types=1);

namespace KboPublicSearch\Establishment\Type\Primitive;

use KboPublicSearch\Core\Utility\XmlText;
use Stringable;
use Webmozart\Assert\Assert;

/** The name of an establishment unit, matched exactly. */
final readonly class EstablishmentName implements Stringable
{
    private function __construct(private string $value)
    {
        Assert::lengthBetween($value, 1, 160, 'An establishment name must be between 1 and 160 characters.');
        XmlText::assert($value, 'An establishment name');
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
