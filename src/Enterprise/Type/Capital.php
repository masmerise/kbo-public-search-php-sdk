<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Enterprise\Type\Primitive\Amount;

/** The capital of an enterprise. */
final readonly class Capital
{
    /** @internal */
    public function __construct(
        public Amount $amount,
        /** Optional in the XSD. */
        public ?Currency $currency,
    ) {}
}
