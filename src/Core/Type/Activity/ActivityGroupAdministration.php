<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Activity;

use KboPublicSearch\Core\Type\Description;

/** The administration that registered an activity (ActivityGroupAdministration). */
final readonly class ActivityGroupAdministration
{
    /** @internal */
    public function __construct(
        /** The ActivityGroupAdministration code, e.g. "006" (RSZ-activiteiten). */
        public string $code,
        public Description $description,
    ) {}
}
