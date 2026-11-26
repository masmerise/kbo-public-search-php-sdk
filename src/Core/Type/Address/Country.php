<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Address;

use KboPublicSearch\Core\Type\Description;

/** The country of a foreign address. */
final readonly class Country
{
    /** @internal */
    public function __construct(
        public CountryCode $code,
        public Description $description,
    ) {}
}
