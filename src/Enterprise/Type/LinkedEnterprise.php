<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Period;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;

/** A relation with another enterprise, e.g. "is split into". */
final readonly class LinkedEnterprise
{
    /** @internal */
    public function __construct(
        public LinkedEnterpriseCode $code,
        public Description $description,
        /** EnterpriseNumberSubject: the left-hand side of the relation. */
        public EnterpriseNumber $enterpriseNumberSubject,
        /** EnterpriseNumberObject: the right-hand side of the relation. */
        public EnterpriseNumber $enterpriseNumberObject,
        public Period $period,
    ) {}
}
