<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Address;

use KboPublicSearch\Core\Type\Description;

/** What kind of address an Address is. */
final readonly class TypeOfAddress
{
    /** @internal */
    public function __construct(
        public TypeOfAddressCode $code,
        public Description $description,
    ) {}
}
