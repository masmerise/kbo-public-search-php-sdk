<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Address;

use KboPublicSearch\Core\Type\LocalizedText;

/** The country of a foreign address. */
final readonly class Country
{
    /** @internal */
    public function __construct(
        public string $code,
        public LocalizedText $name,
    ) {}
}
