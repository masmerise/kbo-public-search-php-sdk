<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Shared;

/** The country of a foreign address. */
final readonly class Country
{
    /** @internal */
    public function __construct(
        public string $code,
        public LocalizedText $name,
    ) {}
}
