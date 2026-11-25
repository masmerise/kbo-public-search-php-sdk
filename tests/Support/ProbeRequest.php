<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Support;

use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Xml\Node;

final class ProbeRequest extends Request
{
    public function __construct(private readonly string $enterpriseNumber) {}

    public function operation(): string
    {
        return 'ReadEnterprise';
    }

    public function message(): array
    {
        return [Node::element('dat:EnterpriseNumber', $this->enterpriseNumber)];
    }
}
