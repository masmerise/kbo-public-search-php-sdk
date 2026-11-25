<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Http\Soap;

use KboPublicSearch\Core\Failure\KboPublicSearchException;
use KboPublicSearch\Core\Failure\UnknownException;
use KboPublicSearch\Core\Http\Result;
use Saloon\Http\Response;

/** @internal */
final readonly class UnwrapResponse
{
    /** @throws KboPublicSearchException Faults usually arrive as HTTP 500, so the status code is ignored. */
    public function __invoke(Response $response): Result
    {
        try {
            return $response->body() |> Envelope::unwrap(...);
        } catch (UnknownException $ex) {
            throw UnknownException::unexpectedReply($response->status(), $response->body(), $ex);
        }
    }
}
