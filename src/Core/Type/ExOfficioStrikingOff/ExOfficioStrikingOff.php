<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\ExOfficioStrikingOff;

use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Period;

/** An ex officio striking off ("ambtshalve doorhaling"). */
final readonly class ExOfficioStrikingOff
{
    /** @internal */
    public function __construct(
        public ExOfficioStrikingOffCode $code,
        public Description $description,
        public ?Period $validityPeriod,
    ) {}
}
