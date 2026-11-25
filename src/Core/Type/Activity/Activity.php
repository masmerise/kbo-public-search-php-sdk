<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Activity;

use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Period;

/** A NACEBEL activity. */
final readonly class Activity
{
    /** @internal */
    public function __construct(
        /** The NACEBEL code. */
        public string $code,
        public ?ActivityGroupAdministration $activityGroupAdministration,
        /** The raw Version, kept when it is not a known NACEBEL version. */
        public string $version,
        public ?NacebelVersion $nacebelVersion,
        public Description $description,
        public Period $period,
        public ?ActivityClassification $activityClassification,
    ) {}
}
