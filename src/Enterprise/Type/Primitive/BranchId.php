<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type\Primitive;

use Stringable;
use Webmozart\Assert\Assert;

/**
 * Links a branch to its enterprise (datamodel.xsd BanchIdType). KBO accepts it in no request (cookbook §2.1.2), so it
 * has no route into one.
 */
final readonly class BranchId implements Stringable
{
    /** @internal */
    public function __construct(private string $value)
    {
        Assert::regex($value, '/^[09]?\d{9}$/', 'A branch id must consist of 9 or 10 digits.');
    }

    public function equals(BranchId $other): bool
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
