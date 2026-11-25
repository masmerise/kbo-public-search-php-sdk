<?php declare(strict_types=1);

namespace KboPublicSearch\Entity\Type;

use KboPublicSearch\Entity\Type\Primitive\ActivityCode;
use Webmozart\Assert\Assert;

/** 1 to 10 NACEBEL codes or prefixes. */
final readonly class ActivityFilter
{
    /** @param list<ActivityCode> $codes */
    private function __construct(private array $codes)
    {
        Assert::countBetween($codes, 1, 10, 'Between 1 and 10 activity codes must be given.');
    }

    public static function of(string ...$codes): self
    {
        return new self(array_map(ActivityCode::fromString(...), array_values($codes)));
    }

    /** @return list<string> */
    public function toArray(): array
    {
        return array_map(static fn (ActivityCode $code) => $code->toString(), $this->codes);
    }
}
