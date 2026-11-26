<?php declare(strict_types=1);

namespace KboPublicSearch\Establishment\Find\Request;

use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Type\Primitive\EstablishmentNumber;
use KboPublicSearch\Core\Xml\Node;
use KboPublicSearch\Establishment\Type\TypeOfResult;

/** @internal */
final class ReadEstablishmentByEstablishmentNumberRequest extends Request
{
    public function __construct(
        private readonly EstablishmentNumber $number,
        private readonly TypeOfResult $detail,
    ) {}

    public function operation(): string
    {
        return 'ReadEstablishmentByEstablishmentNumber';
    }

    public function message(): array
    {
        return [
            Node::element('dat:EstablishmentNumber', $this->number->toString()),
            Node::element('mes:TypeOfResult', $this->detail->value),
        ];
    }
}
