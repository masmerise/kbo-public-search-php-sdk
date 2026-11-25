<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

/** The capital of an enterprise. */
final readonly class Capital
{
    /** @internal */
    public function __construct(
        /** The decimal amount as KBO returns it, e.g. "1000000000.00". */
        public string $amount,
        public string $currency,
    ) {}
}
