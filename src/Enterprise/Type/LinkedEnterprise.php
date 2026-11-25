<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Type\Shared\LocalizedText;
use KboPublicSearch\Core\Type\Shared\Period;

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
