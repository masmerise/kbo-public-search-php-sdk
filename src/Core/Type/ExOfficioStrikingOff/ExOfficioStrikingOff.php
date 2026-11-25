<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\ExOfficioStrikingOff;

use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Primitive\Date;

/** An ex officio striking off ("ambtshalve doorhaling"). */
final readonly class ExOfficioStrikingOff
{
    /** @internal */
    public function __construct(
        public string $code,
        public Description $description,
        /** ValidityPeriod > Begin. */
        public ?Date $since,
    ) {}
}
