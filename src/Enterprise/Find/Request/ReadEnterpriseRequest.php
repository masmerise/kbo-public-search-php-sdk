<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Find\Request;

use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Xml\Node;

/** @internal */
final class ReadEnterpriseRequest extends Request
{
    public function __construct(private readonly EnterpriseNumber $number) {}

    public function operation(): string
    {
        return 'ReadEnterprise';
    }

    public function message(): array
    {
        return [Node::element('dat:EnterpriseNumber', $this->number->toString())];
    }
}
