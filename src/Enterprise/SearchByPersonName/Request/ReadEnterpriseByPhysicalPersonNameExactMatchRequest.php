<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\SearchByPersonName\Request;

use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Core\Xml\Node;
use KboPublicSearch\Enterprise\Type\Primitive\FirstName;
use KboPublicSearch\Enterprise\Type\Primitive\LastName;

/** @internal */
final class ReadEnterpriseByPhysicalPersonNameExactMatchRequest extends Request
{
    public function __construct(
        private readonly LastName $lastName,
        private readonly ?FirstName $firstName,
        private readonly ?Zipcode $zipcode,
        private readonly bool $onlyActive,
    ) {}

    public function operation(): string
    {
        return 'ReadEnterpriseByPhysicalPersonNameExactMatch';
    }

    public function message(): array
    {
        return [
            Node::element('mes:LastName', $this->lastName->toString()),
            ...($this->firstName ? [Node::element('mes:FirstName', $this->firstName->toString())] : []),
            ...($this->zipcode ? [Node::element('mes:Zipcode', $this->zipcode->toString())] : []),
            Node::element('mes:ActiveFilter', $this->onlyActive ? 'true' : 'false'),
        ];
    }
}
