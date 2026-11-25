<?php declare(strict_types=1);

namespace KboPublicSearch\Establishment\SearchByName\Request;

use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Core\Xml\Node;
use KboPublicSearch\Establishment\Type\EstablishmentName;

/** @internal */
final class ReadEstablishmentByNameExactMatchRequest extends Request
{
    public function __construct(
        private readonly EstablishmentName $name,
        private readonly ?Zipcode $zipcode,
        private readonly bool $onlyActive,
    ) {}

    public function operation(): string
    {
        return 'ReadEstablishmentByNameExactMatch';
    }

    public function message(): array
    {
        return [
            Node::element('mes:EstablishmentName', $this->name->toString()),
            ...($this->zipcode ? [Node::element('mes:Zipcode', $this->zipcode->toString())] : []),
            Node::element('mes:ActiveFilter', $this->onlyActive ? 'true' : 'false'),
        ];
    }
}
