<?php declare(strict_types=1);

namespace KboPublicSearch\CodeList\Type;

use KboPublicSearch\Core\Type\Collection;

/**
 * The AUTHORIZATION_SEARCH_CODE code list, sorted on the first requested language.
 *
 * @extends Collection<AuthorizationSearchCodeItem>
 */
final readonly class AuthorizationSearchCodes extends Collection
{
    public function ofKind(EntityKind $kind): self
    {
        return self::of(array_values(array_filter($this->items, static fn (AuthorizationSearchCodeItem $item) => $item->entityKind === $kind)));
    }
}
