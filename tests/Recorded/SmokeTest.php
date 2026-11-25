<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Recorded;

use KboPublicSearch\Core\Http\KboConnector;
use KboPublicSearch\Core\Http\Result;
use KboPublicSearch\Tests\RecordedTestCase;
use KboPublicSearch\Tests\Support\ProbeRequest;
use PHPUnit\Framework\Attributes\Test;

final class SmokeTest extends RecordedTestCase
{
    #[Test]
    public function read_enterprise_round_trip(): void
    {
        // Arrange
        $this->record(ProbeRequest::class, 'smoke/read-enterprise');
        $connector = new KboConnector(self::credentials());
        $connector->useEnvironment(self::environment());

        // Act
        $result = $connector->send(new ProbeRequest(self::ENTERPRISE))->dto();

        // Assert
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->context->status->isSuccess(), "{$result->context->statusCode} {$result->context->statusDescription}");
    }
}
