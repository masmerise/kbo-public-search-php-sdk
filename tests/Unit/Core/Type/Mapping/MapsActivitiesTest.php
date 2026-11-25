<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Activity\ActivityClassificationKind;
use KboPublicSearch\Core\Type\Activity\NaceVersion;
use KboPublicSearch\Core\Type\Mapping\MapsActivities;
use KboPublicSearch\Localization\Language;
use KboPublicSearch\Tests\Support\Snippet;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class MapsActivitiesTest extends TestCase
{
    use MapsActivities;

    #[Test]
    public function maps_the_cookbook_activity(): void
    {
        // Act
        $activity = $this->toActivities(Snippet::load('shared/activities'))[0];

        // Assert
        $this->assertSame('68321', $activity->code);
        $this->assertSame('006', $activity->group?->code);
        $this->assertSame('Activités ONSS', $activity->group?->description->in(Language::French));
        $this->assertSame('2008', $activity->versionCode);
        $this->assertSame(NaceVersion::Nace2008, $activity->version);
        $this->assertSame('2008-01-01', $activity->period->begin?->toString());
        $this->assertNull($activity->period->end);
        $this->assertSame(ActivityClassificationKind::Main, $activity->classification?->kind);
        $this->assertSame('MAIN', $activity->classification?->code);
    }

    #[Test]
    public function unknown_version_and_classification_keep_the_raw_code(): void
    {
        // Act
        $activity = $this->toActivities(Snippet::load('shared/activities'))[1];

        // Assert
        $this->assertNull($activity->version);
        $this->assertSame('20025', $activity->versionCode);
        $this->assertSame('NEWC', $activity->classification?->code);
        $this->assertNull($activity->classification?->kind);
    }

    #[Test]
    public function main_filters_main_activities(): void
    {
        // Act
        $main = $this->toActivities(Snippet::load('shared/activities'))->main();

        // Assert
        $this->assertCount(1, $main);
        $this->assertSame(ActivityClassificationKind::Main, $main->first()?->classification?->kind);
    }
}
