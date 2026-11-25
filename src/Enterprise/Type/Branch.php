<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Address\Address;
use KboPublicSearch\Core\Type\Denomination\Denominations;
use KboPublicSearch\Core\Type\Period;
use KboPublicSearch\Core\Type\Status\Status;

/** A branch ("bijkantoor") of a foreign enterprise. */
final readonly class Branch
{
    /** @internal */
    public function __construct(
        /** Links the branch to its enterprise; it cannot be used in requests. */
        public string $id,
        public Status $status,
        public ?Address $address,
        public Denominations $denominations,
        public Period $validityPeriod,
    ) {}
}
