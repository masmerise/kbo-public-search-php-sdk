<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Activity;

use KboPublicSearch\Core\Type\Description;

/** Whether an activity is a main, ancillary or secondary activity. */
final readonly class ActivityClassification
{
    /** @internal */
    public function __construct(
        public ActivityClassificationCode $code,
        public Description $description,
    ) {}
}
