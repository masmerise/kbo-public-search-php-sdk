<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Type\Shared\Period;

/** A closed enterprise number replaced by this one. */
final readonly class Replacement
{
    /** @internal */
    public function __construct(
        /** The closed enterprise number this enterprise replaces. */
        public EnterpriseNumber $enterpriseNumber,
        public Period $period,
    ) {}
}
