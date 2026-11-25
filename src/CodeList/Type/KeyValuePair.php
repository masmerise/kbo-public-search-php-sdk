<?php declare(strict_types=1);

namespace KboPublicSearch\CodeList\Type;

/** Additional information on a code list item, e.g. EntityKind = enterprise. */
final readonly class KeyValuePair
{
    /** @internal */
    public function __construct(
        public string $key,
        public string $value,
    ) {}
}
