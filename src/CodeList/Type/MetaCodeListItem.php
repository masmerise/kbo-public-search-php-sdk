<?php declare(strict_types=1);

namespace KboPublicSearch\CodeList\Type;

use KboPublicSearch\Core\Type\Description;

/** An item of the META_CODE_LIST code list: a code list that can be read. */
final readonly class MetaCodeListItem
{
    /** @internal */
    public function __construct(
        public CodeListId $code,
        public Description $description,
    ) {}
}
