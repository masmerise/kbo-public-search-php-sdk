<?php declare(strict_types=1);

namespace KboPublicSearch\CodeList\Read\Request;

use KboPublicSearch\CodeList\Type\CodeListId;
use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Xml\Node;

/** @internal */
final class ReadCodeListRequest extends Request
{
    public function __construct(private readonly CodeListId $id) {}

    public function operation(): string
    {
        return 'ReadCodeList';
    }

    public function message(): array
    {
        return [Node::element('mes:CodeListId', $this->id->value)];
    }
}
