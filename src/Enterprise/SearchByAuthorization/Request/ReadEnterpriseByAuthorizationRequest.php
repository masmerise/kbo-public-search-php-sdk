<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\SearchByAuthorization\Request;

use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Xml\Node;
use KboPublicSearch\Enterprise\Type\Primitive\AuthorizationCode;

/** @internal */
final class ReadEnterpriseByAuthorizationRequest extends Request
{
    public function __construct(
        private readonly AuthorizationCode $code,
        private readonly LocationFilter $location,
        private readonly bool $onlyActive,
    ) {}

    public function operation(): string
    {
        return 'ReadEnterpriseByAuthorization';
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
