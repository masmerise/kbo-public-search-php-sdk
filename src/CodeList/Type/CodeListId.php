<?php declare(strict_types=1);

namespace KboPublicSearch\CodeList\Type;

/** The code lists ReadCodeList serves; MetaCodeList lists the others. */
enum CodeListId: string
{
    case MetaCodeList = 'META_CODE_LIST';
    case AuthorizationSearchCode = 'AUTHORIZATION_SEARCH_CODE';
}
