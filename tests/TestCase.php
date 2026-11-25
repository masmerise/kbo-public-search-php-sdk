<?php declare(strict_types=1);

namespace KboPublicSearch\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Saloon\Http\Faking\MockClient;
use Saloon\MockConfig;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        MockClient::destroyGlobal();
        MockConfig::setFixturePath(__DIR__ . '/Fixtures/Saloon');

        if (getenv('CI')) {
            MockConfig::throwOnMissingFixtures();
        }
    }
}
