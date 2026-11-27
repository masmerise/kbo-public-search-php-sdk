<?php declare(strict_types=1);

namespace KboPublicSearch\EnterpriseOrEstablishment\Type\Primitive;

use Stringable;
use Webmozart\Assert\Assert;

/** A NACEBEL 2025 code or prefix of 3 to 7 digits; activities starting with it match. */
final readonly class ActivityCode implements Stringable
{
    private function __construct(private string $code)
    {
        Assert::regex($code, '/^\d{3,7}$/', 'An activity code must consist of 3 to 7 digits.');
    }

    public static function fromString(string $code): self
    {
        return new self($code);
    }

    public function toString(): string
    {
        return $this->code;
    }

    public function __toString(): string
    {
        return $this->code;
    }
}
