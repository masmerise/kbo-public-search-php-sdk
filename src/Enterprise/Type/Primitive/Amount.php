<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type\Primitive;

use Stringable;
use Webmozart\Assert\Assert;

/** The amount of a capital (xs:decimal), kept as KBO writes it, e.g. "1000000000.00", so no precision is lost. */
final readonly class Amount implements Stringable
{
    /** @internal */
    public function __construct(private string $value)
    {
        Assert::regex($value, '/^[+-]?(\d+(\.\d*)?|\.\d+)$/', 'An amount must be a decimal number.');
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
