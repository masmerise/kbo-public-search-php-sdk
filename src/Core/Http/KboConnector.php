<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Http;

use KboPublicSearch\Authentication\Credentials;
use KboPublicSearch\Core\Http\Soap\WrapRequest;
use KboPublicSearch\Environments\Environment;
use KboPublicSearch\Localization\Languages;
use Saloon\Http\Connector;
use Saloon\Http\PendingRequest;
use SensitiveParameter;

/** @internal */
final class KboConnector extends Connector
{
    private Environment $environment = Environment::Production;

    private Languages $languages;

    public function __construct(#[SensitiveParameter] private readonly Credentials $credentials)
    {
        $this->languages = Languages::dutch();
    }

    public function boot(PendingRequest $pendingRequest): void
    {
        $pendingRequest->middleware()->onRequest(new WrapRequest($this->credentials, $this->languages));
    }

    public function resolveBaseUrl(): string
    {
        return $this->environment->url();
    }

    public function useEnvironment(Environment $environment): void
    {
        $this->environment = $environment;
    }

    public function useLanguages(Languages $languages): void
    {
        $this->languages = $languages;
    }

    protected function defaultConfig(): array
    {
        return [
            'timeout' => 30,
        ];
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'text/xml; charset=utf-8',
        ];
    }
}
