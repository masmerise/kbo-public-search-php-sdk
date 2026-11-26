<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Activity\Activities;
use KboPublicSearch\Core\Type\Activity\Activity;
use KboPublicSearch\Core\Type\Activity\ActivityClassification;
use KboPublicSearch\Core\Type\Activity\ActivityClassificationCode;
use KboPublicSearch\Core\Type\Activity\ActivityGroup;
use KboPublicSearch\Core\Type\Activity\ActivityGroupAdministration;
use KboPublicSearch\Core\Type\Activity\NacebelVersion;
use KboPublicSearch\Core\Type\Primitive\NacebelCode;
use KboPublicSearch\Core\Xml\Element;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
final readonly class ActivityMapper
{
    public function __construct(
        private DescriptionMapper $descriptionMapper = new DescriptionMapper(),
        private PeriodMapper $periodMapper = new PeriodMapper(),
    ) {}

    private function toActivity(Element $activity): Activity
    {
        return new Activity(
            code: NacebelCode::fromString($activity->textOf('Code')),
            activityGroupAdministration: transform($activity->optional('ActivityGroupAdministration'), fn (Element $group) => new ActivityGroupAdministration(
                ActivityGroup::from($group->textOf('ActivityGroup')),
                $this->descriptionMapper->toDescription($group),
            )),
            version: NacebelVersion::from($activity->textOf('Version')),
            description: $this->descriptionMapper->toDescription($activity),
            period: $this->periodMapper->toRequiredPeriod($activity, 'Period'),
            activityClassification: transform($activity->optional('ActivityClassification'), fn (Element $classification) => new ActivityClassification(
                ActivityClassificationCode::from($classification->textOf('Code')),
                $this->descriptionMapper->toDescription($classification),
            )),
        );
    }

    public function toActivities(Element $parent): Activities
    {
        return Activities::of(array_map($this->toActivity(...), $parent->children('Activity')));
    }
}
