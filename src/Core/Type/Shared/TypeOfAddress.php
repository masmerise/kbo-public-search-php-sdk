<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Shared;

/** What kind of address an Address is. */
final readonly class TypeOfAddress
{
    /** @internal */
    public function __construct(
        public string $code,
        /** Null for a code this SDK does not know yet. */
        public ?AddressKind $kind,
        public LocalizedText $description,
    ) {}
}
