<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Period;
use KboPublicSearch\Core\Type\Primitive\Date;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;

/** A function ("functie"), e.g. a director. */
final readonly class EnterpriseFunction
{
    /** @internal */
    public function __construct(
        public string $code,
        public Description $description,
        public Period $period,
        public ?Person $person,
        /** The enterprise exercising the function, if any. */
        public ?EnterpriseNumber $enterpriseNumber,
        /** The enterprise number of a natural person who is a professional syndic. */
        public ?EnterpriseNumber $professionalSyndic,
        public ?FunctionCac $cac,
        public ?Date $strikingOff,
        /** ActiveFlag; not used by KBO yet. */
        public ?bool $active,
    ) {}
}
