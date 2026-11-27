<?php declare(strict_types=1);

namespace KboPublicSearch\Tests;

use Dotenv\Dotenv;
use KboPublicSearch\Authentication\Credentials;
use KboPublicSearch\Environments\Environment;
use KboPublicSearch\KboPublicSearch;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Request;
use Saloon\MockConfig;

/**
 * Runs the full SDK against recorded KBO replies.
 *
 * A missing fixture is recorded on the first run with the credentials in tests/.env, against the environment named by
 * KBO_ENVIRONMENT ("test" = acceptance, the default, or "production"); later runs replay offline.
 */
abstract class RecordedTestCase extends TestCase
{
    /** The enterprise the recorded tests revolve around (settled by the first live round-trip). */
    public const string ENTERPRISE = '1037799832';

    /** The establishment unit of ENTERPRISE (from establishment/of-enterprise). */
    public const string ESTABLISHMENT = '2389061005';

    protected KboPublicSearch $kbo;

    protected function setUp(): void
    {
        parent::setUp();

        Dotenv::createImmutable(__DIR__)->safeLoad();

        $this->kbo = KboPublicSearch::authenticate(self::credentials());

        self::environment() === Environment::Production ? $this->kbo->useProduction() : $this->kbo->useTest();
    }

    protected static function credentials(?string $password = null): Credentials
    {
        return Credentials::present($_ENV['KBO_USERNAME'] ?? 'replay', $password ?? $_ENV['KBO_PASSWORD'] ?? 'replay');
    }

    protected static function environment(): Environment
    {
        return ($_ENV['KBO_ENVIRONMENT'] ?? 'test') === 'production' ? Environment::Production : Environment::Test;
    }

    /**
     * Replays the fixture, or records it when it is missing.
     *
     * Recording needs real credentials and an explicit environment; without them Saloon would send a request with
     * the placeholder credentials and store the authentication fault it gets back.
     *
     * @param class-string<Request> $requestClass
     */
    protected function record(string $requestClass, string $fixture): void
    {
        if (! is_file(MockConfig::getFixturePath() . "/{$fixture}.json")) {
            foreach (['KBO_USERNAME', 'KBO_PASSWORD', 'KBO_ENVIRONMENT'] as $variable) {
                if (($_ENV[$variable] ?? '') === '') {
                    $this->fail("Fixture {$fixture} is missing. Set {$variable} in tests/.env to record it (see tests/.env.example).");
                }
            }
        }

        MockClient::global([$requestClass => new KboFixture($fixture)]);
    }
}
