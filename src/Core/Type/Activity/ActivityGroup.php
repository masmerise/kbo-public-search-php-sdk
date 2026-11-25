<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Activity;

use KboPublicSearch\Core\Type\LocalizedText;

/** The administration that registered an activity (ActivityGroupAdministration). */
final readonly class ActivityGroup
{
    /** @internal */
    public function __construct(
        /** The ActivityGroup code, e.g. "006" (RSZ-activiteiten). */
        public string $code,
        public LocalizedText $description,
    ) {}
}
