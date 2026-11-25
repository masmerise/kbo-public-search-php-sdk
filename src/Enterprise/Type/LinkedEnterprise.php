<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\LocalizedText;
use KboPublicSearch\Core\Type\Period;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;

/** A relation with another enterprise, e.g. "is split into". */
final readonly class LinkedEnterprise
{
    /** @internal */
    public function __construct(
        public string $code,
        public LocalizedText $description,
        /** EnterpriseNumberSubject: the left-hand side of the relation. */
        public EnterpriseNumber $subject,
        /** EnterpriseNumberObject: the right-hand side of the relation. */
        public EnterpriseNumber $object,
        public Period $period,
    ) {}
}
