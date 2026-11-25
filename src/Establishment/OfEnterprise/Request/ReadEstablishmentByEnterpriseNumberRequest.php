<?php declare(strict_types=1);

namespace KboPublicSearch\Establishment\OfEnterprise\Request;

use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Type\Parameter\Detail;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Xml\Node;

/** @internal */
final class ReadEstablishmentByEnterpriseNumberRequest extends Request
{
    public function __construct(
        private readonly EnterpriseNumber $number,
        private readonly Detail $detail,
    ) {}

    public function operation(): string
    {
        return 'ReadEstablishmentByEnterpriseNumber';
    }

    public function message(): array
    {
        return [
            Node::element('dat:EnterpriseNumber', $this->number->toString()),
            Node::element('mes:TypeOfResult', $this->detail->value),
        ];
    }
}
