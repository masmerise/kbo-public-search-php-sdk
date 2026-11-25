<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit;

use KboPublicSearch\Tests\RecordedTestCase;
use KboPublicSearch\Tests\Support\ProbeRequest;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Saloon\Http\Faking\MockClient;

/** Recording a missing fixture without credentials would silently store an authentication fault. */
final class RecordedTestCaseTest extends RecordedTestCase
{
    /** @var array<string, mixed> */
    private array $env;

    protected function setUp(): void
    {
        parent::setUp();

        $this->env = $_ENV;
    }

    protected function tearDown(): void
    {
        $_ENV = $this->env;

        parent::tearDown();
    }

    #[Test]
    #[DataProvider('incompleteSetups')]
    public function refuses_to_record_without_a_complete_setup(string $unset): void
    {
        // Arrange
        $_ENV['KBO_USERNAME'] = 'someone';
        $_ENV['KBO_PASSWORD'] = 'secret';
        $_ENV['KBO_ENVIRONMENT'] = 'production';
        unset($_ENV[$unset]);

        // Act
        try {
            $this->record(ProbeRequest::class, 'missing/never-recorded');
            $this->fail('Expected the recording to be refused.');
        } catch (AssertionFailedError $ex) {
            // Assert
            $this->assertStringContainsString('missing/never-recorded', $ex->getMessage());
            $this->assertStringContainsString('tests/.env', $ex->getMessage());
            $this->assertNull(MockClient::getGlobal());
        }
    }

    public static function incompleteSetups(): iterable
    {
        yield 'no username' => ['KBO_USERNAME'];
        yield 'no password' => ['KBO_PASSWORD'];
        yield 'no environment' => ['KBO_ENVIRONMENT'];
    }

    #[Test]
    public function replays_an_existing_fixture_without_credentials(): void
    {
        // Arrange
        unset($_ENV['KBO_USERNAME'], $_ENV['KBO_PASSWORD'], $_ENV['KBO_ENVIRONMENT']);

        // Act
        $this->record(ProbeRequest::class, 'smoke/read-enterprise');

        // Assert
        $this->assertNotNull(MockClient::getGlobal());
    }
}
