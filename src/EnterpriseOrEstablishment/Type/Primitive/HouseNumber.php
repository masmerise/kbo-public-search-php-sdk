<?php declare(strict_types=1);

namespace KboPublicSearch\EnterpriseOrEstablishment\Type\Primitive;

use KboPublicSearch\Core\Utility\XmlText;
use Stringable;
use Webmozart\Assert\Assert;

/** A house number, as used in address searches. */
final readonly class HouseNumber implements Stringable
{
    private function __construct(private string $value)
    {
        Assert::lengthBetween($value, 1, 11, 'A house number must be between 1 and 11 characters.');
        XmlText::assert($value, 'A house number');
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
