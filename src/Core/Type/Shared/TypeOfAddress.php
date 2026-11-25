<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Shared;

/** What kind of address an Address is. */
final readonly class TypeOfAddress
{
    /** @internal */
    public function __construct(
        public AddressKind $kind,
        public LocalizedText $description,
    ) {}
}
