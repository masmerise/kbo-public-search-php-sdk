<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Activity;

use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Period;
use KboPublicSearch\Core\Type\Primitive\NacebelCode;

/** A NACEBEL activity. */
final readonly class Activity
{
    /** @internal */
    public function __construct(
        /** The NACEBEL code, to be read in $version. */
        public NacebelCode $code,
        public ?ActivityGroupAdministration $activityGroupAdministration,
        public NacebelVersion $version,
        public Description $description,
        public Period $period,
        public ?ActivityClassification $activityClassification,
    ) {}
}
