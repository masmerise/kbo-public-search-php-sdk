<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\SearchByCompanyName\Request;

use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Core\Xml\Node;
use KboPublicSearch\Enterprise\Type\JuridicalFormFilter;
use KboPublicSearch\Enterprise\Type\Primitive\CompanyName;

/** @internal */
final class ReadEnterpriseLegalPersonByCompanyNameExactMatchRequest extends Request
{
    public function __construct(
        private readonly CompanyName $name,
        private readonly ?Zipcode $zipcode,
        private readonly ?JuridicalFormFilter $juridicalForm,
        private readonly bool $onlyActive,
    ) {}

    public function operation(): string
    {
        return 'ReadEnterpriseLegalPersonByCompanyNameExactMatch';
    }

    public function message(): array
    {
        return [
            Node::element('mes:CompanyName', $this->name->toString()),
            ...($this->zipcode ? [Node::element('mes:Zipcode', $this->zipcode->toString())] : []),
            ...($this->juridicalForm ? [Node::element('mes:EnterpriseJuridicalForm', $this->juridicalForm->value)] : []),
            Node::element('mes:ActiveFilter', $this->onlyActive ? 'true' : 'false'),
        ];
    }
}
