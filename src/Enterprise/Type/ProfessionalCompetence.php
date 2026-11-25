<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\LocalizedText;
use KboPublicSearch\Core\Type\Period;
use KboPublicSearch\Core\Type\Primitive\Date;

/** A professional competence ("beroepsbekwaamheid"). */
final readonly class ProfessionalCompetence
{
    /** @internal */
    public function __construct(
        public string $code,
        public LocalizedText $description,
        public Period $period,
        /** Exemption > Begin. */
        public ?Date $exemptionSince,
        public ?Date $strikingOff,
    ) {}
}
