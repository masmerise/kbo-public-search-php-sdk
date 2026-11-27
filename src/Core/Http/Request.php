<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Http;

use KboPublicSearch\Core\Http\Soap\UnwrapResponse;
use KboPublicSearch\Core\Xml\Node;
use Saloon\Enums\Method;
use Saloon\Http\Request as RequestBase;
use Saloon\Http\Response;

/** @internal */
abstract class Request extends RequestBase
{
    protected Method $method = Method::POST;

    /** The SOAP operation, e.g. "ReadEnterprise". */
    abstract public function operation(): string;

    /** @return list<Node> The children of mes:{operation}Request, in XSD sequence order. */
    abstract public function message(): array;

    public function createDtoFromResponse(Response $response): UnwrappedReply
    {
        return $response |> new UnwrapResponse();
    }

    public function resolveEndpoint(): string
    {
        return '';
    }
}
