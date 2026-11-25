<?php declare(strict_types=1);

namespace KboPublicSearch;

use KboPublicSearch\Authentication\Credentials;
use KboPublicSearch\CodeList\CodeListResource;
use KboPublicSearch\Core\Http\KboConnector;
use KboPublicSearch\Enterprise\EnterpriseResource;
use KboPublicSearch\Entity\EntityResource;
use KboPublicSearch\Environments\Environment;
use KboPublicSearch\Establishment\EstablishmentResource;
use KboPublicSearch\Localization\Languages;
use KboPublicSearch\Street\StreetResource;
use SensitiveParameter;

final readonly class KboPublicSearch
{
    /** The KBO Public Search cookbook release this SDK implements. */
    public const string VERSION = 'R018.08';

    public CodeListResource $codeLists;

    public EnterpriseResource $enterprises;

    public EntityResource $entities;

    public EstablishmentResource $establishments;

    public StreetResource $streets;

    private KboConnector $client;

    private function __construct(#[SensitiveParameter] Credentials $credentials)
    {
        $this->client = new KboConnector($credentials);
        $this->enterprises = new EnterpriseResource($this->client);
        $this->establishments = new EstablishmentResource($this->client);
        $this->entities = new EntityResource($this->client);
        $this->streets = new StreetResource($this->client);
        $this->codeLists = new CodeListResource($this->client);
    }

    public static function authenticate(#[SensitiveParameter] Credentials $credentials): self
    {
        return new self($credentials);
    }

    /** Return descriptions in Dutch. This is the default. */
    public function useDutch(): self
    {
        $this->client->useLanguages(Languages::dutch());

        return $this;
    }

    /** Return descriptions in both Dutch and French. */
    public function useDutchAndFrench(): self
    {
        $this->client->useLanguages(Languages::dutchAndFrench());

        return $this;
    }

    /** Return descriptions in French. */
    public function useFrench(): self
    {
        $this->client->useLanguages(Languages::french());

        return $this;
    }

    /** Send subsequent requests to the production environment. This is the default. */
    public function useProduction(): self
    {
        $this->client->useEnvironment(Environment::Production);

        return $this;
    }

    /** Send subsequent requests to the acceptance (test) environment. */
    public function useTest(): self
    {
        $this->client->useEnvironment(Environment::Test);

        return $this;
    }
}
