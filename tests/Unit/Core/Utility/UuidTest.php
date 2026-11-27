<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Utility;

use KboPublicSearch\Core\Utility\Uuid;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class UuidTest extends TestCase
{
    #[Test]
    public function generates_random_v4_uuids(): void
    {
        // Act
        $uuid = Uuid::v4();

        // Assert
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid);
        $this->assertNotSame($uuid, Uuid::v4());
    }
}
