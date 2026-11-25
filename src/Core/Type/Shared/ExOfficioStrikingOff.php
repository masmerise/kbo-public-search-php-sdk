<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Shared;

use KboPublicSearch\Core\Type\Primitive\Date;

/** An ex officio striking off ("ambtshalve doorhaling"). */
final readonly class ExOfficioStrikingOff
{
    /** @internal */
    public function __construct(
        public string $code,
        public LocalizedText $description,
        /** ValidityPeriod > Begin. */
        public ?Date $since,
    ) {}
}
