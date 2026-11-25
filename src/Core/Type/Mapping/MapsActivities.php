<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Shared\Activities;
use KboPublicSearch\Core\Type\Shared\Activity;
use KboPublicSearch\Core\Type\Shared\ActivityClassification;
use KboPublicSearch\Core\Type\Shared\ActivityClassificationKind;
use KboPublicSearch\Core\Type\Shared\ActivityGroup;
use KboPublicSearch\Core\Type\Shared\NaceVersion;
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
