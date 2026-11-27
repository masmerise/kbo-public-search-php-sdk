<?php declare(strict_types=1);

namespace KboPublicSearch\Street\Search\Request;

use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Core\Xml\Node;
use KboPublicSearch\Street\Type\Primitive\PartOfStreetName;

/** @internal */
final class ReadStreetNameRequest extends Request
{
    public function __construct(
        private readonly Zipcode $zipcode,
        private readonly PartOfStreetName $part,
    ) {}

    public function operation(): string
    {
        return 'ReadStreetName';
    }

    public function message(): array
    {
        return [
            Node::element('mes:Zipcode', $this->zipcode->toString()),
            Node::element('mes:PartOfStreetName', $this->part->toString()),
        ];
    }
}
