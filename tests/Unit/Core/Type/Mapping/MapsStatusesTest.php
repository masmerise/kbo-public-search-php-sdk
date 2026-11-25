<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Mapping\MapsStatuses;
use KboPublicSearch\Core\Type\Status\StatusKind;
use KboPublicSearch\Tests\Support\Snippet;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class MapsStatusesTest extends TestCase
{
    use MapsStatuses;

    #[Test]
    public function known_status(): void
    {
        // Act
        $status = $this->toStatus(Snippet::load('shared/statuses')->children('Status')[0]);

        // Assert
        $this->assertSame('AC', $status->code);
        $this->assertSame(StatusKind::Active, $status->kind);
        $this->assertSame('Actief', $status->description->first());
    }

    #[Test]
    public function unknown_status_keeps_the_raw_code(): void
    {
        // Act
        $status = $this->toStatus(Snippet::load('shared/statuses')->children('Status')[1]);

        // Assert
        $this->assertSame('XX', $status->code);
        $this->assertNull($status->kind);
    }
}
