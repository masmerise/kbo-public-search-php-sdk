<?php declare(strict_types=1);

namespace KboPublicSearch\CodeList\Type\Mapping;

use KboPublicSearch\CodeList\Type\CodeList;
use KboPublicSearch\CodeList\Type\CodeListItem;
use KboPublicSearch\CodeList\Type\KeyValuePair;
use KboPublicSearch\Core\Type\Mapping\MapsDescriptions;
use KboPublicSearch\Core\Xml\Element;

/** @internal */
trait MapsCodeLists
{
    use MapsDescriptions;

    private function toCodeList(Element $parent): CodeList
    {
        return CodeList::of(array_map($this->toCodeListItem(...), $parent->children('CodeListItem')));
    }

    private function toCodeListItem(Element $item): CodeListItem
    {
        return new CodeListItem(
            code: $item->textOf('Code'),
            description: $this->toDescription($item),
            keyValuePairs: array_map(
                static fn (Element $pair) => new KeyValuePair($pair->optionalText('Key') ?? '', $pair->optionalText('Value') ?? ''),
                $item->children('KeyValuePair'),
            ),
        );
    }
}
