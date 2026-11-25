<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Primitive\Date;
use KboPublicSearch\Core\Type\Shared\LocalizedText;
use KboPublicSearch\Core\Type\Shared\Period;

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
