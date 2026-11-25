<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Period;
use KboPublicSearch\Core\Type\Primitive\Date;

/** A professional competence ("beroepsbekwaamheid"). */
final readonly class ProfessionalCompetence
{
    /** @internal */
    public function __construct(
        public string $code,
        public Description $description,
        public Period $period,
        public ?Period $exemption,
        public ?Date $strikingOff,
    ) {}
}
