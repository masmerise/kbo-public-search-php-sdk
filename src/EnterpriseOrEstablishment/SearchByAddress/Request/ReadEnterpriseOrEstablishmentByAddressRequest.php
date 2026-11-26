<?php declare(strict_types=1);

namespace KboPublicSearch\EnterpriseOrEstablishment\SearchByAddress\Request;

use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Type\Primitive\StreetName;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Core\Xml\Node;
use KboPublicSearch\EnterpriseOrEstablishment\Type\Primitive\HouseNumber;

/** @internal */
final class ReadEnterpriseOrEstablishmentByAddressRequest extends Request
{
    public function __construct(
        private readonly Zipcode $zipcode,
        private readonly StreetName $street,
        private readonly HouseNumber $houseNumber,
        private readonly bool $onlyActive,
    ) {}

    public function operation(): string
    {
        return 'ReadEnterpriseOrEstablishmentByAddress';
    }

    public function message(): array
    {
        return [
            Node::element('mes:Zipcode', $this->zipcode->toString()),
            Node::element('mes:StreetName', $this->street->toString()),
            Node::element('mes:HouseNumber', $this->houseNumber->toString()),
            Node::element('mes:ActiveFilter', $this->onlyActive ? 'true' : 'false'),
        ];
    }
}
