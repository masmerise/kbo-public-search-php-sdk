<?php declare(strict_types=1);

namespace KboPublicSearch\CodeList\Type\Mapping;

use KboPublicSearch\CodeList\Type\AuthorizationSearchCodeItem;
use KboPublicSearch\CodeList\Type\AuthorizationSearchCodes;
use KboPublicSearch\CodeList\Type\CodeListId;
use KboPublicSearch\CodeList\Type\EntityKind;
use KboPublicSearch\CodeList\Type\MetaCodeList;
use KboPublicSearch\CodeList\Type\MetaCodeListItem;
use KboPublicSearch\Core\Type\Mapping\DescriptionMapper;
use KboPublicSearch\Core\Type\Primitive\AuthorizationSearchCode;
use KboPublicSearch\Core\Xml\Element;
use RuntimeException;

/** @internal */
final readonly class CodeListMapper
{
    public function __construct(
        private DescriptionMapper $descriptionMapper = new DescriptionMapper(),
    ) {}

    public function toAuthorizationSearchCodes(Element $parent): AuthorizationSearchCodes
    {
        return AuthorizationSearchCodes::of(array_map(
            fn (Element $item) => new AuthorizationSearchCodeItem(
                AuthorizationSearchCode::fromString($item->textOf('Code')),
                $this->descriptionMapper->toDescription($item),
                EntityKind::from($this->keyValue($item, 'EntityKind')),
            ),
            $parent->children('CodeListItem'),
        ));
    }

    public function toMetaCodeList(Element $parent): MetaCodeList
    {
        return MetaCodeList::of(array_map(
            fn (Element $item) => new MetaCodeListItem(CodeListId::from($item->textOf('Code')), $this->descriptionMapper->toDescription($item)),
            $parent->children('CodeListItem'),
        ));
    }

    /** @throws RuntimeException when the item has no pair with this key */
    private function keyValue(Element $item, string $key): string
    {
        foreach ($item->children('KeyValuePair') as $pair) {
            if ($pair->optionalText('Key') === $key) {
                return $pair->optionalText('Value') ?? '';
            }
        }

        throw new RuntimeException("The code list item {$item->textOf('Code')} has no {$key}.");
    }
}
