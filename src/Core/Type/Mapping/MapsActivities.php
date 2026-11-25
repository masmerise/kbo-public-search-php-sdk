<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Activity\Activities;
use KboPublicSearch\Core\Type\Activity\Activity;
use KboPublicSearch\Core\Type\Activity\ActivityClassification;
use KboPublicSearch\Core\Type\Activity\ActivityClassificationKind;
use KboPublicSearch\Core\Type\Activity\ActivityGroupAdministration;
use KboPublicSearch\Core\Type\Activity\NacebelVersion;
use KboPublicSearch\Core\Xml\Element;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
trait MapsActivities
{
    use MapsDescriptions;
    use MapsPeriods;

    private function toActivity(Element $activity): Activity
    {
        $version = $activity->optionalText('Version') ?? '';

        return new Activity(
            code: $activity->textOf('Code'),
            activityGroupAdministration: transform($activity->optional('ActivityGroupAdministration'), fn (Element $group) => new ActivityGroupAdministration(
                $group->optionalText('ActivityGroup') ?? '',
                $this->toDescription($group),
            )),
            version: $version,
            nacebelVersion: NacebelVersion::tryFrom($version),
            description: $this->toDescription($activity),
            period: $this->toRequiredPeriod($activity, 'Period'),
            activityClassification: transform($activity->optional('ActivityClassification'), fn (Element $classification) => new ActivityClassification(
                $code = $classification->textOf('Code'),
                ActivityClassificationKind::tryFrom($code),
                $this->toDescription($classification),
            )),
        );
    }

    private function toActivities(Element $parent): Activities
    {
        return Activities::of(array_map($this->toActivity(...), $parent->children('Activity')));
    }
}
