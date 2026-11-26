<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Activity;

use KboPublicSearch\Core\Type\Collection;

/**
 * Activities.
 *
 * @extends Collection<Activity>
 */
final readonly class Activities extends Collection
{
    /** The main activities (classification MAIN). */
    public function main(): self
    {
        return self::of(array_values(array_filter(
            $this->items,
            static fn (Activity $activity) => $activity->activityClassification?->code === ActivityClassificationCode::Main,
        )));
    }
}
