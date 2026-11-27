<?php declare(strict_types=1);

namespace KboPublicSearch\CodeList\Type;

use KboPublicSearch\Core\Type\Description;
use KboPublicSearch\Core\Type\Primitive\AuthorizationSearchCode;

/** An item of the AUTHORIZATION_SEARCH_CODE code list. */
final readonly class AuthorizationSearchCodeItem
{
    /** @internal */
    public function __construct(
        public AuthorizationSearchCode $code,
        public Description $description,
        /** Pass the code to enterprises->searchByAuthorization() or to establishments->searchByAuthorization(). */
        public EntityKind $entityKind,
    ) {}
}
