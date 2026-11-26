<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Mapping\StatusMapper;
use KboPublicSearch\Core\Type\Status\StatusCode;
use KboPublicSearch\Tests\Support\Snippet;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ValueError;

final class StatusMapperTest extends TestCase
{
    #[Test]
    public function known_status(): void
    {
        // Act
        $status = $this->statusMapper()->toStatus(Snippet::load('shared/statuses')->children('Status')[0]);

        // Assert
        $this->assertSame(StatusCode::Active, $status->code);
        $this->assertSame('Actief', $status->description->first());
    }

    #[Test]
    public function unknown_status_fails(): void
    {
        // Assert
        $this->expectException(ValueError::class);

        // Act
        $this->statusMapper()->toStatus(Snippet::load('shared/unknown/status')->child('Status'));
    }

    private function statusMapper(): StatusMapper
    {
        return new StatusMapper();
    }
}
