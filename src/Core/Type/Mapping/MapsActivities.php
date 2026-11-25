<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Activity\Activities;
use KboPublicSearch\Core\Type\Activity\Activity;
use KboPublicSearch\Core\Type\Activity\ActivityClassification;
use KboPublicSearch\Core\Type\Activity\ActivityClassificationKind;
use KboPublicSearch\Core\Type\Activity\ActivityGroup;
use KboPublicSearch\Core\Type\Activity\NaceVersion;
use KboPublicSearch\Core\Xml\Element;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
trait MapsActivities
{
    use MapsLocalizedTexts;
    use MapsPeriods;

    private function toActivity(Element $activity): Activity
    {
        $version = $activity->optionalText('Version') ?? '';

        return new Activity(
            code: $activity->textOf('Code'),
            group: transform($activity->optional('ActivityGroupAdministration'), fn (Element $group) => new ActivityGroup(
                $group->optionalText('ActivityGroup') ?? '',
                $this->toLocalizedText($group),
            )),
            versionCode: $version,
            version: NaceVersion::tryFrom($version),
            description: $this->toLocalizedText($activity),
            period: $this->toRequiredPeriod($activity, 'Period'),
            classification: transform($activity->optional('ActivityClassification'), fn (Element $classification) => new ActivityClassification(
                $code = $classification->textOf('Code'),
                ActivityClassificationKind::tryFrom($code),
                $this->toLocalizedText($classification),
            )),
        );
    }

    private function toActivities(Element $parent): Activities
    {
        return Activities::of(array_map($this->toActivity(...), $parent->children('Activity')));
    }
}
