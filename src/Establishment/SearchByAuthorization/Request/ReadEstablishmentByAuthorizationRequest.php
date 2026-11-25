<?php declare(strict_types=1);

namespace KboPublicSearch\Establishment\SearchByAuthorization\Request;

use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Primitive\AuthorizationSearchCode;
use KboPublicSearch\Core\Xml\Node;

/** @internal */
final class ReadEstablishmentByAuthorizationRequest extends Request
{
    public function __construct(
        private readonly AuthorizationSearchCode $code,
        private readonly LocationFilter $location,
        private readonly bool $onlyActive,
    ) {}

    public function operation(): string
    {
        return 'ReadEstablishmentByAuthorization';
    }

    public function message(): array
    {
        return [
            Node::element('mes:AuthorizationFilter', $this->code->toString()),
            $this->location->toNode(),
            Node::element('mes:ActiveFilter', $this->onlyActive ? 'true' : 'false'),
        ];
    }
}
