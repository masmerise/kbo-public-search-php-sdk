<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Activity;

use KboPublicSearch\Core\Type\Description;

/** The administration that registered an activity (ActivityGroupAdministration). */
final readonly class ActivityGroupAdministration
{
    /** @internal */
    public function __construct(
        public ActivityGroup $activityGroup,
        public Description $description,
    ) {}
}
