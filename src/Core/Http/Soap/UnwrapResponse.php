<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Http\Soap;

use KboPublicSearch\Core\Failure\KboPublicSearchException;
use KboPublicSearch\Core\Http\Result;
use Saloon\Http\Response;

/** @internal */
final readonly class UnwrapResponse
{
    /** @throws KboPublicSearchException Faults usually arrive as HTTP 500, so the status code is ignored. */
    public function __invoke(Response $response): Result
    {
        return $response->body() |> Envelope::unwrap(...);
    }
}
