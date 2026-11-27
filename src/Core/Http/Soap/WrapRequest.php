<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Http\Soap;

use DateTimeImmutable;
use KboPublicSearch\Authentication\Credentials;
use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Utility\Uuid;
use KboPublicSearch\Localization\Languages;
use LogicException;
use Saloon\Http\PendingRequest;
use Saloon\Repositories\Body\StringBodyRepository;
use SensitiveParameter;

/** @internal */
final readonly class WrapRequest
{
    public function __construct(
        #[SensitiveParameter] private Credentials $credentials,
        private Languages $languages,
    ) {}

    public function __invoke(PendingRequest $pendingRequest): void
    {
        $request = $pendingRequest->getRequest();

        if (! $request instanceof Request) {
            throw new LogicException('Only KBO requests can be sent through the KBO connector.');
        }

        $envelope = Envelope::wrap(
            operation: $request->operation(),
            message: $request->message(),
            credentials: $this->credentials,
            languages: $this->languages,
            now: new DateTimeImmutable(),
            rawNonce: random_bytes(16),
            requestId: Uuid::v4(),
        );

        $pendingRequest->headers()->add('SOAPAction', "http://fgov.economie.be/kbopub/{$request->operation()}");
        $pendingRequest->setBody(new StringBodyRepository($envelope));
    }
}
