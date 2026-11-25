<?php declare(strict_types=1);

namespace KboPublicSearch\Tests;

use Dotenv\Dotenv;
use KboPublicSearch\Authentication\Credentials;
use KboPublicSearch\Environments\Environment;
use KboPublicSearch\KboPublicSearch;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Request;

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

    /** @param class-string<Request> $requestClass */
    protected function record(string $requestClass, string $fixture): void
    {
        MockClient::global([$requestClass => new KboFixture($fixture)]);
    }
}
