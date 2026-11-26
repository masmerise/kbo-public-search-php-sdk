<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Address\Address;
use KboPublicSearch\Core\Type\Denomination\Denominations;
use KboPublicSearch\Core\Type\Period;
use KboPublicSearch\Core\Type\Status\Status;
use KboPublicSearch\Enterprise\Type\Primitive\BranchId;

/** A branch ("bijkantoor") of a foreign enterprise. */
final readonly class Branch
{
    /** @internal */
    public function __construct(
        public BranchId $id,
        public Status $status,
        public ?Address $address,
        public Denominations $denominations,
        public Period $validityPeriod,
    ) {}
}
