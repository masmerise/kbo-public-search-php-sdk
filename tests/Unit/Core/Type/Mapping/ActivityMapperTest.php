<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Activity\ActivityClassificationCode;
use KboPublicSearch\Core\Type\Activity\ActivityGroup;
use KboPublicSearch\Core\Type\Activity\NacebelVersion;
use KboPublicSearch\Core\Type\Mapping\ActivityMapper;
use KboPublicSearch\Localization\Language;
use KboPublicSearch\Tests\Support\Snippet;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ValueError;

final class ActivityMapperTest extends TestCase
{
    #[Test]
    public function maps_the_cookbook_activity(): void
    {
        // Act
        $activity = $this->activityMapper()->toActivities(Snippet::load('shared/activities'))[0];

        // Assert
        $this->assertSame('68321', $activity->code->toString());
        $this->assertSame(ActivityGroup::ActivitiesNSSO, $activity->activityGroupAdministration?->activityGroup);
        $this->assertSame('Activités ONSS', $activity->activityGroupAdministration?->description->in(Language::French));
        $this->assertSame(NacebelVersion::Nacebel2008, $activity->version);
        $this->assertSame('2008-01-01', $activity->period->begin?->toString());
        $this->assertNull($activity->period->end);
        $this->assertSame(ActivityClassificationCode::Main, $activity->activityClassification?->code);
    }

    #[Test]
    public function unknown_version_or_classification_fails(): void
    {
        // Assert
        $this->expectException(ValueError::class);

        // Act
        $this->activityMapper()->toActivities(Snippet::load('shared/unknown/activity'));
    }

    #[Test]
    public function main_filters_main_activities(): void
    {
        // Act
        $main = $this->activityMapper()->toActivities(Snippet::load('shared/activities'))->main();

        // Assert
        $this->assertCount(1, $main);
        $this->assertSame(ActivityClassificationCode::Main, $main->first()?->activityClassification?->code);
    }

    private function activityMapper(): ActivityMapper
    {
        return new ActivityMapper();
    }
}
